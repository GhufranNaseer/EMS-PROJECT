# Exhibition Management System (EMS) - Deployment & Operations Guide

This document is the official operational blueprint for deploying, configuring, maintaining, and troubleshooting the **Exhibition Management System (EMS)** in both local development environments and high-performance production servers (VPS).

---

# 14. Configuration Documentation

### 14.1 Key Configuration Files
1. **`www/application/config/config.php`:**
   - **`base_url`:** Set dynamically to `http://` or `https://` depending on `$_SERVER['HTTP_HOST']`. For local development, this evaluates to `http://localhost:8000/`.
   - **`sess_save_path`:** Set to `APPPATH . 'cache/'` to ensure Windows/Linux cross-platform directory mapping works.
2. **`www/application/config/database.php`:**
   - Connection properties (hostname, username, password, database) for the Active Record instance.
3. **`custom-php.ini`** (`www/custom-php.ini`):
   - Custom php runtime options:
     ```ini
     post_max_size = 25M
     upload_max_filesize = 10M
     memory_limit = 2000M
     max_execution_time = 2000
     ```

### 14.2 Constants & Secrets Management
Core variables such as email SMTP configurations, project names, and directories are stored in `www/application/config/constants.php` and `www/application/config/config.php`.
- **`encryption_key`:** Set in `config.php` as `Kx3piZIuin5He31fGN9elUk8fn7bKCMm`. Used to encrypt and decrypt cookies, sessions, and recovery links.

---

# 15. Local Development Setup

Follow these exact steps to run the application on your local machine without Docker.

### 15.1 System Prerequisites
Ensure your local host has the following runtimes active:
- **PHP:** `7.4` to `8.2` (requires `mysqli`, `gd`, `curl`, and `mbstring` extensions enabled in `php.ini`).
- **MySQL / MariaDB:** `8.0` / `10.x` or later.
- **Web server:** Apache (with `mod_rewrite` enabled) or PHP CLI for built-in serving.

### 15.2 Database Import
1. Access your MySQL terminal:
   ```powershell
   mysql -u root -p -e "CREATE DATABASE ems_main_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```
2. Import the SQL transactional structure:
   ```powershell
   mysql -u root -p ems_main_db < d:\exhibition\exhibition_system\exhibition_system.sql
   ```

### 15.3 Setup Credentials
Open `www/application/config/database.php` and match the credentials to your local instance:
```php
'hostname' => '127.0.0.1', // explicit IPv4 loopback
'username' => 'root',
'password' => '',
'database' => 'ems_main_db',
```

### 15.4 Starting the Development Server
Execute the built-in PHP server referencing the custom `.htaccess` rewrite simulator script (`router.php`):
```powershell
cd d:\exhibition\exhibition_system\www
php -S localhost:8000 router.php
```
Open your browser and navigate to **`http://localhost:8000`**. Log in using:
- **Email:** `admin@admin.com`
- **Password:** `admin123`

---

# 16. Git & Version Control Strategy

EMS uses a structured, standardized Git branching model based on GitFlow:

```
[production]   ◄────────────────────────────────── (Hotfix pushes)
     ▲
     │ (Merge on release tags)
  [main]       ◄────────────────────────────────── (Main branch for stable tags)
     ▲
     │ (Staging merges)
  [develop]    ◄────────────────── (Nightly build verification)
   ▲  ▲  ▲
   │  │  │ (Feature Merges)
   │  │  └─── [feature/reCaptcha-removal]
   │  └────── [feature/dynamic-WAMP-db]
   └───────── [feature/built-in-routing]
```

### 16.1 Development Workflow Rules
1. **Never commit directly to `main` or `develop`.** All work must be conducted in dedicated feature branches (`feature/xxx`).
2. **Commit Messages Format:** Follow Conventional Commits:
   - `feat: ...` for new features.
   - `fix: ...` for bug fixes.
   - `docs: ...` for documentation.
   - `chore: ...` for structural refactorings.

---

# 17. CI/CD Pipeline Design

The following YAML schema defines a standard automated GitHub Actions workflow to build, test, and securely deploy EMS to a VPS on code push to the `main` branch.

```yaml
# .github/workflows/deploy.yml
name: EMS Continuous Integration & Deployment

on:
  push:
    branches: [ main ]

jobs:
  build-and-test:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout Codebase
        uses: actions/checkout@v3

      - name: Setup PHP Environment
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
          extensions: mysqli, gd, curl, mbstring, xml, zip
          coverage: none

      - name: Install Composer Dependencies
        run: |
          cd www
          composer install --no-interaction --prefer-dist --optimize-autoloader

      - name: Execute PHP Linter (Syntax Validation)
        run: find www/application/ -name "*.php" -print0 | xargs -0 -n1 php -l

  deploy-to-vps:
    needs: build-and-test
    runs-on: ubuntu-latest
    steps:
      - name: Checkout Code
        uses: actions/checkout@v3

      - name: Deploy Code via SSH rsync
        uses: easingthemes/ssh-deploy@main
        env:
          SSH_PRIVATE_KEY: ${{ secrets.VPS_SSH_KEY }}
          ARGS: "-rlgoDzvc -i --delete"
          SOURCE: "www/"
          REMOTE_HOST: ${{ secrets.VPS_HOST }}
          REMOTE_USER: "deploy"
          TARGET: "/var/www/ems"

      - name: Execute Remote Post-Deployment Tasks
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.VPS_HOST }}
          username: "deploy"
          key: ${{ secrets.VPS_SSH_KEY }}
          script: |
            sudo chown -R www-data:www-data /var/www/ems
            sudo chmod -R 755 /var/www/ems
            sudo chmod -R 777 /var/www/ems/application/cache
            sudo systemctl reload nginx
            sudo systemctl restart php7.4-fpm
```

---

# 18. VPS Deployment Guide

Below is the complete blueprint to host the application on a clean **Ubuntu 20.04/22.04 LTS** VPS.

### 18.1 Package Installation
```bash
sudo apt update
sudo apt install -y nginx mariadb-server php-fpm php-mysql php-gd php-curl php-mbstring php-zip php-xml unzip rsync git
```

### 18.2 Database Configuration & Setup
1. Lock down MariaDB/MySQL:
   ```bash
   sudo mysql_secure_installation
   ```
2. Create schema and database user:
   ```bash
   sudo mysql -u root -p
   ```
   ```sql
   CREATE DATABASE ems_main_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'ems_web_user'@'localhost' IDENTIFIED BY 'EWEpYe3TJTTT@gKl';
   GRANT ALL PRIVILEGES ON ems_main_db.* TO 'ems_web_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```
3. Load initial database:
   ```bash
   mysql -u ems_web_user -pEWEpYe3TJTTT@gKl ems_main_db < /var/www/ems/exhibition_system.sql
   ```

### 18.3 Configure Nginx Virtual Host
Create the configuration file `/etc/nginx/sites-available/ems`:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/ems;
    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?/$request_uri;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|otf)$ {
        expires max;
        log_not_found off;
    }
}
```
Link and reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/ems /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 18.4 Permissions & Directories setup
Configure web server group owners:
```bash
sudo chown -R www-data:www-data /var/www/ems
sudo chmod -R 755 /var/www/ems
sudo chmod -R 777 /var/www/ems/application/cache
```

### 18.5 Configure Transactional Cron Tasks
Configure the recurring background mailing task:
```bash
# Open crontab file
crontab -e
```
Add the following line to run the CodeIgniter controller via CLI every 5 minutes:
```cron
*/5 * * * * php /var/www/ems/index.php welcome cron_email >/dev/null 2>&1
```

---

# 19. Troubleshooting Guide

### 19.1 `404 Not Found` on Navigation Links
- **Symptom:** Opening the home page works, but clicking any `.html` page results in Nginx or Apache 404.
- **Cause:** URL rewriting is disabled or not configured.
- **Fix:**
  - *On Apache:* Verify `AllowOverride All` is set in your Apache vhost config and the `.htaccess` file exists in `www/`.
  - *On Nginx:* Verify your `try_files` rule includes `/index.php?/$request_uri` (as shown in section 18.3).
  - *On Local Server:* Make sure you launched the server using the custom rewrite script: `php -S localhost:8000 router.php`.

### 19.2 Database Connection Failures
- **Symptom:** Browser displays standard CodeIgniter DB error output: "Unable to connect to your database server...".
- **Cause:** MySQL is bound to IPv6 or listening on a different port.
- **Fix:** In `database.php`, change `'hostname'` from `'localhost'` to explicit IPv4 loopback `'127.0.0.1'`. Verify MySQL is active (`sudo systemctl status mysql`).

### 19.3 File Upload Failures
- **Symptom:** Profiler image upload fails or folder error is displayed.
- **Cause:** Write permissions are absent on `www/uploads/` directory.
- **Fix:** Run `chmod -R 777 /var/www/ems/uploads` on Linux, or right-click the folder on Windows → Properties → uncheck "Read-only".
