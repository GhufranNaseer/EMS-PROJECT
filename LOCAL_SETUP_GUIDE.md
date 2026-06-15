# EMS Project - Local Setup Guide

## Prerequisites

* Laragon installed and running
* EMS project source code
* `exhibition_system.sql` database dump

---

# 1. Extract Project

Extract the project into:

```text
C:\laragon\www\EMS-PROJECT\
```

Expected structure:

```text
EMS-PROJECT/
├── www/
├── exhibition_system.sql
├── docker-compose.yml
└── README.md
```

---

# 2. Create Database

Create a database named:

```text
ems_main_db1
```

Import:

```text
exhibition_system.sql
```

Verify tables are created successfully.

---

# 3. Configure Base URL

## File

```text
C:\laragon\www\EMS-PROJECT\www\application\config\config.php
```

## Search

```php
$config['base_url'] = $protocol.$_SERVER['HTTP_HOST'].'/';
```

## Replace With

```php
$config['base_url'] = $protocol . $_SERVER['HTTP_HOST'] . '/EMS-PROJECT/www/';
```

## Purpose

Ensures routing, asset loading, AJAX requests, and login redirects work correctly.

---

# 4. Configure RewriteBase

## File

```text
C:\laragon\www\EMS-PROJECT\www\.htaccess
```

## Search

```apache
RewriteBase /
```

## Replace With

```apache
RewriteBase /EMS-PROJECT/www/
```

## Purpose

Required for CodeIgniter URL rewriting and authentication routes.

---

# 5. Verify Database Configuration

## File

```text
C:\laragon\www\EMS-PROJECT\www\application\config\database.php
```

## Locate

```php
$db['default'] = array(
```

## Verify Values

```php
'hostname' => '127.0.0.1',
'username' => 'root',
'password' => '',
'database' => 'ems_main_db1',
'dbdriver' => 'mysqli',
```

Update only if your local environment uses different credentials.

---

# 6. Start Application

Start Apache and MySQL from Laragon.

Open:

```text
http://localhost/EMS-PROJECT/www/
```

Expected result:

* Login page loads successfully.
* No PHP errors.
* CSS and JS assets load correctly.

---

# 7. Login

Default credentials:

```text
Email:    super.admin@email.com
Password: test123
```

Successful login should redirect to the dashboard.

---

# Verification Checklist

| Item        | Expected Result     |
| ----------- | ------------------- |
| Apache      | Running             |
| MySQL       | Running             |
| Database    | ems_main_db1 exists |
| SQL Import  | Tables available    |
| Base URL    | Updated             |
| RewriteBase | Updated             |
| Login       | Successful          |

---

# Common Issues

## Database Connection Error

### File

```text
application/config/database.php
```

### Verify

```php
'hostname' => '127.0.0.1',
'username' => 'root',
'password' => '',
'database' => 'ems_main_db1',
```

Also verify MySQL is running in Laragon.

---

## 404 Errors

### File

```text
www/.htaccess
```

### Verify

```apache
RewriteBase /EMS-PROJECT/www/
```

Incorrect RewriteBase is the most common cause of routing issues.

---

## Login Not Working

### File

```text
application/config/config.php
```

### Verify

```php
$config['base_url'] = $protocol . $_SERVER['HTTP_HOST'] . '/EMS-PROJECT/www/';
```

Also confirm:

* Database imported correctly.
* Credentials are correct.
* AJAX requests return HTTP 200.

---

## Blank Page

Check:

```text
application/logs/
```

Review:

* Latest CodeIgniter logs
* Browser Console (F12)
* PHP error logs

---

# Important Paths

## Project Root

```text
C:\laragon\www\EMS-PROJECT\www\
```

## Database Configuration

```text
application\config\database.php
```

## Application Configuration

```text
application\config\config.php
```

## Rewrite Rules

```text
www\.htaccess
```

---

# Development Notes

* Source code changes are reflected immediately.
* Check logs before debugging application logic.
* Verify URL configuration before investigating authentication issues.
* Keep a backup of the database before major changes.

---

# Useful URLs

```text
Application:
http://localhost/EMS-PROJECT/www/

phpMyAdmin:
http://localhost/phpmyadmin

Laragon Home:
http://localhost
```

---

# Quick Troubleshooting

Before reporting an issue, verify:

```text
□ Apache is running
□ MySQL is running
□ ems_main_db1 exists
□ SQL import completed successfully
□ config.php base_url updated
□ .htaccess RewriteBase updated
□ Login credentials are correct
□ Browser cache cleared
□ Laragon restarted
```

---

**Document Version:** 2.0
**Audience:** Developers
**Purpose:** Local Development Environment Setup
