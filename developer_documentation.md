# EMS Developer Documentation

A practical developer guide for the **Exhibition Management System (EMS)**. Use this document to understand the project, set it up locally, find the important files, and make changes without getting lost.

---

## 1. Quick Summary

EMS is a CodeIgniter-based exhibition management platform for B2B events, trade shows, and expos.

It helps organizers manage:

- Exhibitor/customer registration
- Stall and hall booking
- Orders, invoices, discounts, taxes, and payments
- Badge data, QR codes, barcodes, and print queues
- Exhibitor self-service portal tasks
- B2B meeting/visitor workflows
- Email and SMS background jobs
- Audit logs for database changes

The application has three main web areas:

| Area | Path | Purpose |
| --- | --- | --- |
| Organizer/Admin Portal | `www/` | Main internal system for organizers, sales, managers, and facilitation staff |
| Exhibitor Portal | `www/client/` | Self-service portal for exhibitors/customers |
| Meeting Portal | `www/meeting/` | B2B matchmaking, visitor, and meeting workflows |

---

## 2. Start Here as a Developer

If you are new to the project, read and inspect these first:

1. `README.md` for the project overview.
2. `www/application/core/MY_Controller.php` to understand login, permissions, routing hooks, and audit logging.
3. `www/application/config/routes.php` to understand URL mapping.
4. `www/application/config/database.php` to check database connection settings.
5. `www/application/controllers/Book_stall.php` to see one of the main business workflows.
6. `www/application/models/` to understand how database access is organized.

Most important rule: **new main-portal controllers should extend `MY_Controller`** so authentication, authorization, and query logging continue to work.

---

## 3. Technology Stack

| Layer | Technology |
| --- | --- |
| Backend language | PHP |
| Framework | CodeIgniter 3.x |
| Database | MySQL 8 / MariaDB 10.x |
| Database driver | `mysqli` |
| Frontend base | AdminLTE 2.3, Bootstrap, jQuery |
| Tables | DataTables with custom server-side processing |
| PDF generation | HTML2PDF |
| Email | PHPMailer |
| Barcode | Picqer Barcode Generator |
| QR service | Python FastAPI service |
| Containers | Docker Compose |
| Web server | Apache in container, usually behind Nginx in production |
| Background jobs | Dedicated cron container |

---

## 4. Main Features

### Stall and Space Booking

The system allows sales and admin users to book stalls, track availability, prevent double booking, and manage tentative or confirmed bookings.

### Orders, Pricing, and Invoices

EMS supports order creation, stall pricing, taxes, discounts, invoice generation, and PDF exports.

### Exhibitor Portal

Exhibitors can log in and manage their own event requirements, including badge lists, visa documents, fascia names, branding, hotels, and other services.

### Badge and On-Site Operations

The system generates QR codes, Code 128 barcodes, badge PDFs, and print jobs for on-site check-in and facilitation workflows.

### B2B Meetings

The meeting portal supports visitor/exhibitor meeting workflows and agenda management.

### Audit Logging

Database write queries are automatically collected and stored in `user_log` after controller execution.

### Cron Jobs

Background jobs process email and SMS queues asynchronously through scheduled endpoints.

---

## 5. User Roles

EMS uses group-based access control through the `users` and `usergroup` tables.

| Role | ID | Access Summary |
| --- | --- | --- |
| Administrator | 1 | Full system access and wildcard permissions |
| Customer / Exhibitor | 2 | Exhibitor portal access for own profile, bookings, invoices, and badge data |
| Sales Person | 3 | Customer registration, stall booking, orders, and visitors |
| Manager | 4 | Sales review and tentative order approval |
| Facilitation Staff | 6 | On-site operations, badge queues, visitor checks, and check-ins |

---

## 6. Repository Structure

```text
EMS-PROJECT/
|-- cron/                         # Cron container files
|   |-- Dockerfile
|   `-- crontab
|
|-- db/                           # Persistent database volume/mount
|
|-- qr_service/                   # Python FastAPI QR service
|   |-- module/
|   |-- Dockerfile
|   |-- main.py
|   |-- requirements.txt
|   `-- vercel.json
|
|-- www/                          # Main web root
|   |-- index.php                  # Main CodeIgniter entry point
|   |-- router.php                 # Local PHP server rewrite helper
|   |-- .htaccess                  # Apache rewrite rules
|   |-- composer.json              # PHP dependencies
|   |-- assets/                    # CSS, JS, fonts, images
|   |-- uploads/                   # Uploaded/generated files
|   |
|   |-- application/               # Organizer/Admin portal app
|   |   |-- config/
|   |   |-- controllers/
|   |   |-- core/
|   |   |-- helpers/
|   |   |-- libraries/
|   |   |-- models/
|   |   |-- third_party/
|   |   `-- views/
|   |
|   |-- client/                    # Exhibitor portal app
|   |   |-- index.php
|   |   `-- application/
|   |
|   `-- meeting/                   # Meeting portal app
|       |-- index.php
|       `-- application/
|
|-- Dockerfile                     # PHP/Apache container
`-- docker-compose.yml             # Multi-container setup
```

---

## 7. Entry Points

| Entry Point | What It Does |
| --- | --- |
| `www/index.php` | Main organizer/admin portal |
| `www/client/index.php` | Exhibitor portal |
| `www/meeting/index.php` | Meeting portal |
| `www/router.php` | Local PHP server rewrite simulation |
| `qr_service/main.py` | QR code API service |
| `cron/crontab` | Scheduled background jobs |

---

## 8. Architecture Overview

EMS follows the classic CodeIgniter MVC pattern:

```text
Browser request
    |
    v
www/index.php
    |
    v
CodeIgniter router
    |
    v
MY_Controller::_remap()
    |
    |-- check login/session
    |-- check ACL permissions
    |-- call controller action
    |
    v
Controller action
    |
    |-- load models
    |-- query database
    |-- prepare view data
    |
    v
View / JSON / PDF response
    |
    v
MY_Controller::__destruct()
    |
    v
Db_log writes mutation queries to user_log
```

### Important Core Classes

| File | Purpose |
| --- | --- |
| `www/application/core/MY_Controller.php` | Session checks, permission checks, remapping, and audit logging trigger |
| `www/application/core/MY_Model.php` | Common model/database wrapper behavior |
| `www/application/models/Db_log.php` | Stores database mutation queries in `user_log` |
| `www/application/libraries/Datatables.php` | Server-side DataTables processing |
| `www/application/libraries/Common.php` | Shared utility methods used across modules |

---

## 9. Request Lifecycle

A normal secured request works like this:

1. Browser requests a URL such as `/stalls.html`.
2. `www/index.php` boots CodeIgniter.
3. CodeIgniter resolves the route/controller.
4. `MY_Controller::_remap()` intercepts the call.
5. The system checks whether the user is logged in.
6. The system checks the user's group permissions.
7. The controller method runs.
8. Models read/write database data.
9. A view, PDF, JSON response, or redirect is returned.
10. At the end of the request, `MY_Controller::__destruct()` logs database write queries.

---

## 10. Important Modules

### Authentication and Authorization

| Item | Location |
| --- | --- |
| Main login controller | `www/application/controllers/Welcome.php` |
| Core auth/ACL logic | `www/application/core/MY_Controller.php` |
| User model | `www/application/models/Usermdl.php` |
| Main tables | `users`, `usergroup`, `temp_value` |

Main responsibilities:

- Validate login credentials
- Create user sessions
- Handle remember-me behavior
- Check permissions before controller actions
- Redirect unauthenticated users to login

### Stall Booking and Orders

Common responsibilities:

- Create and update customer bookings
- Manage stall availability
- Handle tentative vs confirmed orders
- Calculate prices, taxes, discounts, and totals
- Generate order/invoice views and PDFs

Important areas to inspect:

- `www/application/controllers/Book_stall.php`
- `www/application/controllers/Order_list.php`
- Related order, exhibition, stall, and customer models

### Exhibitor Portal

Path: `www/client/`

Exhibitors use this area to manage their own event data. Typical actions include:

- Updating company/profile details
- Submitting badge lists
- Uploading required documents
- Managing visa/fascia/hotel/branding/service requests
- Viewing bookings and invoices

### Meeting Portal

Path: `www/meeting/`

This sub-application handles meeting and matchmaking workflows for visitors, exhibitors, and delegations.

### Badge and Print Jobs

Common responsibilities:

- Generate QR codes and barcodes
- Build badge PDFs
- Queue badge print jobs
- Support on-site check-in workflows

Useful locations:

- `www/application/controllers/Print_job.php`
- `www/uploads/qr-codes/`
- `qr_service/main.py`

### Reports and Exports

Reports usually use the custom DataTables library, then export filtered records as HTML tables with Excel headers.

Typical export headers:

```php
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment;filename=report_name.xls");
header("Cache-Control: max-age=0");
echo $html_table;
```

---

## 11. Database Notes

### Main Database

Default database name used in local setup examples:

```text
ems_main_db1
```

### Configuration File

Database settings live here:

```text
www/application/config/database.php
```

Example local configuration:

```php
$db['default'] = array(
    'hostname' => '127.0.0.1',
    'username' => 'your_mysql_user',
    'password' => 'your_mysql_password',
    'database' => 'ems_main_db1',
    'dbdriver' => 'mysqli',
    // ...
);
```

### Audit Logging

The audit logger stores mutating SQL queries in `user_log`.

Logged query types usually include:

- `INSERT`
- `UPDATE`
- `DELETE`

Be careful when changing database columns. Some older controllers and JavaScript validation helpers rely on exact column names.

---

## 12. Background Jobs

EMS uses a dedicated cron container instead of running cron directly inside the main web container.

```text
cronjob container
    |
    | every minute
    v
Main web container endpoints
    |
    |-- /Cron_email/send_messages
    `-- /Cron_email/send_text_message
```

### Email Queue

Endpoint:

```text
/Cron_email/send_messages
```

Behavior:

- Reads unsent records from `es_emails_cron`
- Sends up to 10 emails per run
- Uses the PHPMailer helper
- Marks successful records as sent
- Updates invitation status for event invitation emails

### SMS Queue

Endpoint:

```text
/Cron_email/send_text_message
```

Behavior:

- Reads unsent records from `es_sms_notification`
- Sends up to 5 SMS messages per run
- Uses `funcs->send_sms()`
- Marks successful records as sent

---

## 13. File Uploads

Uploads are stored under:

```text
www/uploads/
```

Common upload folders:

| Folder | Purpose |
| --- | --- |
| `www/uploads/badge_cnic/` | CNIC/passport scans for badges |
| `www/uploads/exhibition/` | Exhibition branding and banners |
| `www/uploads/profile/` | User profile photos |
| `www/uploads/qr-codes/` | Generated QR/barcode assets |

General upload behavior:

- Only allowed extensions should be accepted.
- File size limits come from upload config and PHP settings.
- Generated filenames should avoid collisions.
- Database records usually store relative upload paths.

---

## 14. PDF and Badge Generation

### Invoice PDFs

Invoice PDFs are usually generated from order/invoice controller actions. The system builds an HTML template and passes it into HTML2PDF.

Common entry point:

```text
Order_list/print_order_invoice
```

### Badge PDFs

Badge PDFs are generated for physical card stock and include QR/barcode assets.

Common entry point:

```text
Print_job/print_badges
```

Typical badge size mentioned in the system:

```text
82.74mm x 48.26mm
```

---

## 15. Local Development Setup

### Prerequisites

Install or enable:

- PHP 7.4 or 8.1
- MySQL 8 or MariaDB 10.x
- Apache with `mod_rewrite`, or PHP CLI server
- PHP extensions: `mysqli`, `gd`, `curl`, `mbstring`, `zip`, `xml`

### Create Database

```sql
CREATE DATABASE ems_main_db1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Import Database

From the project root:

```bash
mysql -u root -p ems_main_db1 < exhibition_system.sql
```

### Configure Database

Edit:

```text
www/application/config/database.php
```

Set your local username, password, and database name.

### Run Local PHP Server

From the `www` directory:

```powershell
cd www
php -S localhost:8000 router.php
```

Open:

```text
http://localhost:8000
```

Developer login:

| Field | Value |
| --- | --- |
| Email | `admin@admin.com` |
| Password | `admin123` |

---

## 16. Docker Setup

The Docker setup includes these services:

| Service | Purpose |
| --- | --- |
| `webserver` | Apache + PHP application container |
| `mysql-db` | MySQL database container |
| `ems_qr_service` | FastAPI QR generation service |
| `cronjob` | Scheduled email/SMS worker container |

### Start Containers

```bash
docker compose up -d --build
```

### View Web Logs

```bash
docker compose logs -f webserver
```

### Import Database into Docker MySQL

```bash
docker exec -i exhibition_system_mysql-db_1 mysql -u ems_web_user -pEWEpYe3TJTTT@gKl ems_main_db1 < exhibition_system.sql
```

Note: container names can vary depending on your Docker Compose project name. Run `docker compose ps` if the command above does not match your local container name.

---

## 17. Deployment Notes

Typical production setup:

```text
Internet
  |
  v
Nginx reverse proxy / SSL
  |
  v
PHP-FPM or Apache/PHP app
  |
  v
MySQL / MariaDB
```

### Server Requirements

- Ubuntu 20.04 or 22.04 LTS
- Nginx
- PHP-FPM 7.4 or 8.1
- MariaDB/MySQL
- Certbot for SSL
- Required PHP extensions: `mysql`, `gd`, `curl`, `mbstring`, `zip`, `xml`

### Example Package Install

```bash
sudo apt update
sudo apt install -y nginx mariadb-server php-fpm php-mysql php-gd php-curl php-mbstring php-zip php-xml unzip certbot python3-certbot-nginx
```

### Example Nginx Site

```nginx
server {
    listen 80;
    server_name exhibit.yourdomain.com;
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
}
```

### Permissions

```bash
sudo chown -R www-data:www-data /var/www/ems
sudo chmod -R 755 /var/www/ems
sudo chmod -R 777 /var/www/ems/application/cache
sudo chmod -R 777 /var/www/ems/uploads
```

For tighter production security, avoid broad `777` permissions where possible. Prefer granting write access only to the web server user/group.

---

## 18. Testing

The project can use PHPUnit through Composer dependencies in `www/composer.json`.

Run tests from the `www` directory:

```bash
cd www
composer install
./vendor/bin/phpunit
```

If PHPUnit config is missing, check:

```text
www/tests/phpunit.xml
```

---

## 19. Error Handling and Logs

CodeIgniter logging is controlled from:

```text
www/application/config/config.php
```

Common setting:

```php
$config['log_threshold'] = 4;
```

Log files are stored in:

```text
www/application/logs/
```

File format:

```text
log-YYYY-MM-DD.php
```

The `.php` extension helps prevent raw log contents from being directly exposed by the web server.

---

## 20. Known Issues and Technical Debt

### MD5 Password Hashing

Some authentication code uses MD5 password hashing. This is insecure and should be migrated to `password_hash()` and `password_verify()` with bcrypt or Argon2id.

Files to inspect:

- `www/application/controllers/Welcome.php`
- `www/application/models/Usermdl.php`

### Large Controllers

Some controllers contain validation, database logic, file processing, and PDF generation in one place. This makes changes harder and increases regression risk.

Recommended direction:

- Move validation into dedicated validation classes or methods.
- Move business logic into service-style classes.
- Keep controllers focused on request/response handling.

### Repeated Code Across Portals

The main, client, and meeting portals have duplicated libraries/models/helpers in some areas.

Recommended direction:

- Share common libraries through a single reusable location.
- Avoid copying fixes manually across all portal applications.

### Raw SQL and Column Coupling

Some modules depend on exact database column names in raw SQL or JavaScript validation rules.

Before renaming columns:

- Search the whole repository.
- Check controller methods.
- Check models.
- Check `doFormValidation.js` and AJAX validation rules.
- Check report/export code.

---

## 21. Common Development Rules

Follow these rules when adding or changing features:

1. Extend `MY_Controller` for secured main-portal controllers.
2. Check ACL rules before exposing new actions.
3. Use existing model/helper/library patterns where possible.
4. Keep upload paths relative in the database unless existing code requires otherwise.
5. Reuse DataTables patterns for list pages.
6. Reuse HTML2PDF patterns for PDFs.
7. Test changes in the correct portal: main, client, or meeting.
8. Search for duplicated portal code before changing shared behavior.
9. Avoid changing table/column names without a full repository search.
10. Use the internal Docker service URL for QR generation when running in containers:

```text
http://ems_qr_service:8000/api
```

---

## 22. Useful Search Commands

From the project root:

```bash
rg "class .* extends MY_Controller" www/application/controllers
rg "function rule" www/application/controllers
rg "md5\(" www
rg "user_log" www
rg "Cron_email" www
rg "print_badges" www
rg "print_order_invoice" www
```

These commands help you quickly find controllers, ACL rules, password hashing, audit logging, cron handlers, and PDF generation points.

---

## 23. End-to-End Mental Model

Think of EMS as an operational cockpit for exhibitions.

A user logs in, permissions are checked, they perform an action such as booking a stall or printing a badge, the controller coordinates models and views, the database stores the result, and the audit logger records write queries. Background cron jobs then process queued emails and SMS messages.

```text
User
  -> Controller with ACL
  -> Model/database work
  -> View/PDF/JSON response
  -> Audit log
  -> Email/SMS queues handled by cron
```

That is the main flow to keep in mind when debugging or extending the system.
