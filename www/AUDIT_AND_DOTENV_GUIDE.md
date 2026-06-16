# Project Audit & phpdotenv Integration Guide (Roman Urdu)
**Framework Version:** CodeIgniter 3.1.7  
**Project Folder:** `/www/`

Is document me aapke EMS-PROJECT ke security issues ka audit aur `vlucas/phpdotenv` library ko manual chalane ka tareeqa nihayat aasan Roman Urdu me diya gaya hai.

---

## Part 1: Abhi Project Me Kahan Kahan Credentials Hain? (Audit)

EMS-PROJECT me abhi teen (3) alag-alag areas hain jahan database credentials aur security keys direct PHP files ke andar hardcoded likhi hui hain. Jab hum localhost se VPS par code lekar jate hain, toh hume in files ko manually edit karna parta hai.

### Hardcoded Configuration Table:

| File ka Path | Setting / Group | Kya config likha hai | Risk Level | Problem Kya hai? |
| :--- | :--- | :--- | :--- | :--- |
| [`www/application/config/database.php`](file:///c:/laragon/www/EMS-PROJECT/www/application/config/database.php#L76) | `$db['default']` | `127.0.0.1`, `root`, DB name: `ems_main_db1` | **Medium** | Local environment ki information leaks hoti hai. |
| [`www/application/config/database.php`](file:///c:/laragon/www/EMS-PROJECT/www/application/config/database.php#L99) | `$db['live_db']` | IP: `160.153.92.102`, Pass: `aS12!AS12!` | **BOHOT DANGEROUS (CRITICAL)** | Live production database ka real password code me likha hai jo Git pr chala jata hai. |
| [`www/meeting/application/config/database.php`](file:///c:/laragon/www/EMS-PROJECT/www/meeting/application/config/database.php#L76) | `$db['default']` | Host: `mysql-db`, Pass: `EWEpYe3TJTTT@gKl` | **BOHOT DANGEROUS (CRITICAL)** | Live Meeting Database ka password hardcoded hai. |
| [`www/client/application/config/database.php`](file:///c:/laragon/www/EMS-PROJECT/www/client/application/config/database.php#L76) | `$db['default']` | Host: `mysql-db`, Pass: `EWEpYe3TJTTT@gKl` | **BOHOT DANGEROUS (CRITICAL)** | Live Client Database ka password hardcoded hai. |
| [`www/application/config/config.php`](file:///c:/laragon/www/EMS-PROJECT/www/application/config/config.php#L330) | `encryption_key` | `'Kx3piZIuin5He31fGN9elUk8fn7bKCMm'` | **High** | Safe key live server ke liye alag honi chahiye par har jagah same hardcoded hai. |
| [`www/meeting/application/config/config.php`](file:///c:/laragon/www/EMS-PROJECT/www/meeting/application/config/config.php#L328) | `encryption_key` | Same local key hardcoded hai | **High** | Security risk hai. |
| [`www/client/application/config/config.php`](file:///c:/laragon/www/EMS-PROJECT/www/client/application/config/config.php#L328) | `encryption_key` | Same local key hardcoded hai | **High** | Security risk hai. |
| [`www/index.php`](file:///c:/laragon/www/EMS-PROJECT/www/index.php#L63) | `ENVIRONMENT` | `'production'` hardcoded hai | **Medium** | Toggling development/production manually file edit karke karni parti hai. |

---

## Part 2: phpdotenv Lagane Se Kya Behtari Aayegi? (Benefits)

1.  **Code me bar-bar change se nijaat:** Localhost par aap apna alag database rakhein, VPS par live website ka alag database ho. Aapko dono jagah ek baar bhi PHP file edit nahi karni paregi.
2.  **Git security:** Aapke VPS database ke passwords Git repository me push nahi honge. Kal ko agar aap GitHub ya GitLab par code public bhi kar den, tab bhi aapke live passwords safe rahenge.
3.  **Ek Central File:** Pure project ki database details, SMTP email settings, base URLs, aur errors show karne ya chupane ki settings sirf ek file (jis ka naam `.env` hai) se control hongi.

### VPS pe live karte waqt phir kya change karna parega?
*   Aapko kisi PHP file me **kuch bhi change nahi karna**.
*   Aap bas VPS server par ja kar `/www/` folder me ek khali file banayenge `.env` ke naam se.
*   Us `.env` file me VPS ki details daal denge. Code automatic adjust ho jayega.

---

## Part 3: vlucas/phpdotenv Manual Setup ke Steps (Roman Urdu)

### Step 1: Composer ke zariye install karen
Apne computer ke terminal me `/www/` directory me khade ho kar ye command chalayein:
```bash
composer require vlucas/phpdotenv
```
> [!NOTE]
> Is se `/www/vendor/` folder ban jayega jo library file ko control karega.

---

### Step 2: `.env` file ko Git me upload hone se rokna
Hum nahi chahte ke local passwords Git me commit hon. Is ke liye `/www/.gitignore` file ko open karen aur sab se aakhir me ye text add kar ke save kar den:
```gitignore
.env
```

---

### Step 3: Local `.env` File banana
Ab `/www/` folder me ek new file banayein jiska naam rakhein sirf **`.env`** (is ka aage koi extension na ho, jaise `.txt`). Usme ye details likh kar save krden:
```env
# Application Settings
ENVIRONMENT=development
ENCRYPTION_KEY=Kx3piZIuin5He31fGN9elUk8fn7bKCMm
LOG_THRESHOLD=4

# Database Settings
DB_HOST=127.0.0.1
DB_USER=root
DB_PASS=
DB_NAME=ems_main_db1
DB_DRIVER=mysqli
```

---

### Step 4: `.env.example` File banana (Template ke liye)
`/www/` folder me ek aur file banayein **`.env.example`** ke naam se. Is file me passwords nahi honge, ye sirf ek template hoga taake doosre developers ko pta chale ke kaun se variables chahiye:
```env
ENVIRONMENT=production
ENCRYPTION_KEY=
LOG_THRESHOLD=1

DB_HOST=
DB_USER=
DB_PASS=
DB_NAME=
DB_DRIVER=mysqli
```
*(Yeh `.env.example` file Git me push hogi).*

---

### Step 5: Entry points (`index.php`) me loader set karna

CodeIgniter start hone se pehle `.env` ko read karwane ke liye hum entry point files ke start me (right after `<?php` or `date_default_timezone_set`) loader script dalenge:

#### 1. Main Entry point: [www/index.php](file:///c:/laragon/www/EMS-PROJECT/www/index.php)
File kholen aur `date_default_timezone_set('Asia/Karachi');` ke foran baad ye code add kar den:
```php
// Composer autoloader aur Dotenv library register krna
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}
```
*Aur thora neche line ~62-63 pr `ENVIRONMENT` ko replace karen:*
```php
// Old hardcoded replace with:
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: (isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production'));
```

#### 2. Meeting Entry point: [www/meeting/index.php](file:///c:/laragon/www/EMS-PROJECT/www/meeting/index.php)
File kholen aur top par ye code add karen (yeh relative path se parent vendor load karega):
```php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}
```
*Aur `ENVIRONMENT` ko replace karen:*
```php
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: (isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production'));
```

#### 3. Client Entry point: [www/client/index.php](file:///c:/laragon/www/EMS-PROJECT/www/client/index.php)
File kholen aur top par ye code add karen:
```php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}
```
*Aur `ENVIRONMENT` ko replace karen:*
```php
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: (isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production'));
```

---

### Step 6: CodeIgniter Config files me dynamic code dalna

#### 1. Database Files (`database.php`) ko update karna:
In teenon database configuration files ko open karen:
*   `/www/application/config/database.php`
*   `/www/meeting/application/config/database.php`
*   `/www/client/application/config/database.php`

In sab me `$db['default']` configuration ko is tarha dynamic set kar den:
```php
$db['default'] = array(
	'dsn'	=> '',
	'hostname' => getenv('DB_HOST') ?: '127.0.0.1',
	'username' => getenv('DB_USER') ?: 'root',
	'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
	'database' => getenv('DB_NAME') ?: 'ems_main_db1',
	'dbdriver' => getenv('DB_DRIVER') ?: 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
```
*(Main database file me se `$db['live_db']` block ko delete kar den kyunki ab uski zaroorat nahi hai).*

#### 2. Main Config Files (`config.php`) ko update karna:
In teenon config files ko open karen:
*   `/www/application/config/config.php`
*   `/www/meeting/application/config/config.php`
*   `/www/client/application/config/config.php`

In me `encryption_key` aur `log_threshold` ko replace kar den:
```php
$config['log_threshold'] = getenv('LOG_THRESHOLD') !== false ? (int)getenv('LOG_THRESHOLD') : 1;
$config['encryption_key'] = getenv('ENCRYPTION_KEY') ?: 'Kx3piZIuin5He31fGN9elUk8fn7bKCMm';
```

---

## Part 4: VPS pr live kaise karenge?

Jab code live karna ho:
1. Local directory se files VPS par copy karen (is me `www/vendor/` folder shamil hona chahiye).
2. VPS par `/www/` directory me ek `.env` file banayein.
3. Us `.env` file me live details set kar den:
   ```env
   ENVIRONMENT=production
   ENCRYPTION_KEY=NayaWalaSecureLiveKey!
   LOG_THRESHOLD=1

   DB_HOST=127.0.0.1
   DB_USER=vps_database_user
   DB_PASS=VpsDatabasePassword123!
   DB_NAME=exhibition_system
   ```
Aapka system automatic live ho jayega, aur future me code update karte waqt kisi bhi file me passwords change karne ka khatra nahi rahega!
