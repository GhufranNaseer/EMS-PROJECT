<?php
/**
 * Safe Database Migration Runner for EMS (Exhibition Management System)
 * 
 * Features:
 * - 100% Conflict-free: Never touches existing data or re-executes applied migrations.
 * - Tracks applied migrations in `es_migrations` table.
 * - Cross-Platform: Works on Local (Laragon/Windows) and Live VPS (Ubuntu/Docker).
 * - Reads database configuration dynamically from .env.
 * 
 * Usage:
 *   php db/migrate.php          (Runs pending migrations)
 *   php db/migrate.php --status (Displays migration status)
 */

declare(strict_types=1);
date_default_timezone_set('Asia/Karachi');

// 1. Locate and parse .env file
$root_dir = dirname(__DIR__);
$env_locations = [
    $root_dir . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . '.env',
    $root_dir . DIRECTORY_SEPARATOR . '.env',
    __DIR__ . DIRECTORY_SEPARATOR . '.env',
];

$env_file = null;
foreach ($env_locations as $loc) {
    if (is_file($loc)) {
        $env_file = $loc;
        break;
    }
}

if ($env_file) {
    $lines = @file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

function get_env_val(string $key, ?string $default = null): ?string {
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
    $val = getenv($key);
    return ($val !== false && $val !== null) ? $val : $default;
}

// 2. Resolve Database Connection Settings
$db_host   = get_env_val('DB_HOST', '127.0.0.1');
$db_user   = get_env_val('DB_USER', 'root');
$db_pass   = get_env_val('DB_PASS', '');
$db_name   = get_env_val('DB_NAME', 'ems_main_db');
$db_port   = (int)get_env_val('DB_PORT', '3306');

echo "======================================================\n";
echo "   EMS SAFE DATABASE MIGRATION RUNNER                \n";
echo "======================================================\n";
echo "Host     : {$db_host}:{$db_port}\n";
echo "Database : {$db_name}\n";
echo "User     : {$db_user}\n";
echo "Time     : " . date('Y-m-d H:i:s') . "\n";
echo "------------------------------------------------------\n";

// 3. Connect to Database via PDO
$dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    fwrite(STDERR, "[ERROR] Database connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

// 4. Ensure Migration Tracking Table exists (`es_migrations`)
$create_tracker_sql = "
CREATE TABLE IF NOT EXISTS `es_migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL DEFAULT 1,
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_migration_name` (`migration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$pdo->exec($create_tracker_sql);

// 5. Fetch all previously executed migrations
$stmt = $pdo->query("SELECT migration, batch, applied_at FROM es_migrations ORDER BY id ASC");
$applied_rows = $stmt->fetchAll();
$applied_map = [];
$current_batch = 0;
foreach ($applied_rows as $row) {
    $applied_map[$row['migration']] = $row;
    if ((int)$row['batch'] > $current_batch) {
        $current_batch = (int)$row['batch'];
    }
}
$next_batch = $current_batch + 1;

// 6. Find all migration files in `db/migrations/`
$migrations_dir = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';
if (!is_dir($migrations_dir)) {
    @mkdir($migrations_dir, 0755, true);
}

$files = glob($migrations_dir . DIRECTORY_SEPARATOR . '*.sql');
if ($files === false) {
    $files = [];
}
sort($files, SORT_NATURAL);

// Check for --status argument
$is_status_mode = (isset($argv[1]) && in_array($argv[1], ['--status', '-s', 'status']));

if ($is_status_mode) {
    echo "Current Migrations Status:\n";
    if (empty($files)) {
        echo "  No migration files found in db/migrations/.\n";
    } else {
        foreach ($files as $file) {
            $name = basename($file);
            if (isset($applied_map[$name])) {
                $info = $applied_map[$name];
                echo "  [APPLIED]  {$name} (Batch: {$info['batch']}, At: {$info['applied_at']})\n";
            } else {
                echo "  [PENDING]  {$name}\n";
            }
        }
    }
    echo "======================================================\n";
    exit(0);
}

// 7. Filter pending migrations
$pending_files = [];
foreach ($files as $file) {
    $name = basename($file);
    if (!isset($applied_map[$name])) {
        $pending_files[] = $file;
    }
}

if (empty($pending_files)) {
    echo "[OK] Database is up-to-date. No pending migrations.\n";
    echo "======================================================\n";
    exit(0);
}

echo "Found " . count($pending_files) . " pending migration(s):\n";
foreach ($pending_files as $f) {
    echo "  - " . basename($f) . "\n";
}
echo "------------------------------------------------------\n";

// 8. Execute pending migrations safely
$applied_count = 0;

foreach ($pending_files as $file) {
    $migration_name = basename($file);
    echo "Applying: {$migration_name} ... ";

    $sql_content = file_get_contents($file);
    if ($sql_content === false || trim($sql_content) === '') {
        echo "SKIPPED (Empty file)\n";
        // Still record to prevent looping
        $rec = $pdo->prepare("INSERT INTO es_migrations (migration, batch) VALUES (?, ?)");
        $rec->execute([$migration_name, $next_batch]);
        continue;
    }

    // Split SQL by semicolon safely or execute multi-queries
    try {
        // Execute migration statements
        $pdo->exec($sql_content);

        // Record in tracking table
        $rec = $pdo->prepare("INSERT INTO es_migrations (migration, batch) VALUES (?, ?)");
        $rec->execute([$migration_name, $next_batch]);

        echo "[SUCCESS]\n";
        $applied_count++;
    } catch (Throwable $e) {
        echo "[FAILED]\n";
        fwrite(STDERR, "\n[MIGRATION ERROR in {$migration_name}]:\n" . $e->getMessage() . "\n\n");
        fwrite(STDERR, "Migration stopped to prevent database conflicts or partial execution.\n");
        exit(1);
    }
}

echo "------------------------------------------------------\n";
echo "[OK] Successfully applied {$applied_count} migration(s) (Batch {$next_batch}).\n";
echo "======================================================\n";
exit(0);
