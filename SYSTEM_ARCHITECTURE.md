# Exhibition Management System (EMS) - System Architecture & Security Review

This document provides a technical breakdown of the architecture, security posture, code design, technical debt, and scalability vectors of the **Exhibition Management System (EMS)**.

---

# 4. CodeIgniter Architecture Breakdown

### 4.1 Request Lifecycle & Routing Flow
The application implements a front controller design pattern. All incoming requests are routed through `www/index.php`. The request lifecycle proceeds as follows:

```
[User Browser Request]
       │
       ▼
 [www/index.php]  ◄─── (Loads constants & bootstrap paths)
       │
       ▼
 [www/system/core/CodeIgniter.php]  ◄─── (Core Bootstrap / Router)
       │
       ▼
 [www/application/config/routes.php]  ◄─── (URL rewrite mappings)
       │
       ▼
 [Controller _remap() hook]  ◄─── (MY_Controller: Checks session & modular rights)
       │
       ▼
 [Controller Action]  ◄─── (Welcome.php, Portal.php, Stalls.php, etc.)
       │
  ┌────┴────┐
  ▼         ▼
[Models]  [Views]
  │         │
  └────┬────┘
       ▼
[Response Sent to User]
```

### 4.2 Core Controller Architecture (`MY_Controller`)
All standard organizers and admin controllers inherit from the core abstract class **`MY_Controller`** (`www/application/core/MY_Controller.php`).
- **`_remap($method, $params)` Hook:** 
  CodeIgniter automatically delegates method execution to the `_remap()` method when defined in the base class. In EMS, `_remap` intercepts all method queries and evaluates role permissions and authentication scopes BEFORE letting the child controller act.
- **Rules Mapping (`rule()` method):**
  Each controller defines an associative rule array mapping methods to access permissions:
  - `@`: Requires logged-in status (`checklogin`).
  - `-`: Requires not logged-in status (`checkifnotlogin`, e.g. login screens).
  - `*`: Publicly accessible methods (`justcheckifloginornot`).
  - `ajaxOnly => true`: Automatically rejects direct browser requests with a `404` error if the request does not carry the `XMLHttpRequest` headers.

### 4.3 Database Active Record Wrapper (`MY_Model`)
All primary models extend **`MY_Model`** (`www/application/core/MY_Model.php`). 
- It dynamically resolves the table name using the `getTable()` return value or falls back to the class name.
- Provides consistent data operations: `save()`, `getRow()`, `search()`, `insert()`, `update()`, `delete()`.
- Implements transaction logging: `Db_log` is called on `__destruct` to query CodeIgniter's queries array (`$this->db->queries`) and insert records of all data-altering statements (INSERT, UPDATE, DELETE) into the `user_log` table.

---

# 13. Authentication & Security Review

EMS manages sensitive event parameters, financial transactions, and attendee personal data. Below is the assessment of the system security posture:

### 13.1 Authentication Mechanism
- **Core Session State:** Admin and user credentials are saved in CodeIgniter's Session system:
  - User Identifier: `$this->session->userdata('userid')`
  - User Password Hash: `$this->session->userdata('userpassword')`
- **Password Storage:** Passwords are stored in the database hashed using MD5 (`md5($password)`). 
  > [!WARNING]
  > **Security Alert:** MD5 is mathematically broken and vulnerable to collision attacks and rainbow-table decryption. Upgrading to native PHP `password_hash()` (bcrypt/Argon2id) is a critical priority for staging and production environments.

### 13.2 SQL Injection Protection
- The codebase largely leverages CodeIgniter's Active Record Query Builder (`$this->db->where()`, `$this->db->get()`), which automatically escapes input parameters.
- **Vulnerability Check:** Direct SQL string concatenations exist inside `MY_Controller.php` (e.g., custom IP address logging and raw WHERE queries in `getUsernamePasswordWithRme()`). These should be thoroughly refactored to use parameterized bindings.

### 13.3 XSS & Form Security
- Global XSS filtering is turned off by default (`$config['global_xss_filtering'] = FALSE;` in `config.php`) because it is deprecated in CI3.
- Input data should be sanitized on output using `html_escape()` inside the View layer.
- **reCaptcha Protection:** Forms are protected by Google reCaptcha v2 / v3 on production. On local environments (`localhost`, `127.0.0.1`), the verification is bypassed dynamically in `Welcome.php` to prevent deployment blocking.

### 13.4 Session & Cookie Security
- `sess_cookie_name` is set to `ci_session`.
- `cookie_secure` is configured as `FALSE` by default in `config.php`, meaning cookies can be transmitted over unencrypted HTTP.
- **Recommendation:** In production, enable `$config['cookie_secure'] = TRUE;` and `$config['cookie_httponly'] = TRUE;` to prevent session hijacking via malicious JS injections.

---

# 20. Technical Debt Analysis

### 20.1 Large Controllers & Monolithic Blocks
- Controllers like `Officer.php` (53KB, over 1500 lines) and `Book_stall.php` (33KB) contain massive blocks of procedural SQL logic, file upload routines, and validation criteria. 
- **Impact:** Difficult to unit-test or extend without causing unintended side effects.
- **Remedy:** Extract business validation rules into CI Custom Form Validation classes and move transactional logic into a Service Layer (Domain Logic).

### 20.2 Legacy Cryptographic Functions
- High usage of `MD5` hashing for key indexing, passwords, and recover tokens (`MD5(CONCAT(...))` patterns).
- **Remedy:** Refactor with standard PHP core security interfaces, using HMAC and SHA-256 for secure tokens, and `password_hash()` for user passwords.

### 20.3 Multi-Portal Code Redundancy
- The codebase contains duplicate models, libraries, and asset directories across three physical roots: `www/application/`, `www/client/application/`, and `www/meeting/application/`.
- **Impact:** Any updates to core library helpers (like `Common.php`) must be manually duplicated across all three directories, introducing structural drift and bugs.
- **Remedy:** Configure a Shared Application architecture where `client/` and `meeting/` share the parent `application/core/` and `application/libraries/` folders using custom autoload pathways.

---

# 23. Future Scalability Recommendations

### 23.1 High-Availability VPS Architecture
To scale EMS to support massive simultaneous booking sessions (e.g., during live expos where hundreds of agents print badges and scanners constantly ping the DB):

```
                     [Cloudflare / DNS]
                             │
                             ▼
                 [Nginx Reverse Proxy / LB]
                  ┌──────────┴──────────┐
                  ▼                     ▼
            [Web Server 1]        [Web Server 2]
            (PHP-FPM Pool)        (PHP-FPM Pool)
                  └──────────┬──────────┘
                             ▼
                     [Redis Cache Server]
             (Shared sessions & real-time queues)
                             │
                             ▼
                  [MySQL Master-Replica]
                  ┌──────────┴──────────┐
                  ▼                     ▼
             (Write Db)            (Read Db)
```

### 23.2 Performance Tuning Vector
1. **Shared Sessions:** Transition CodeIgniter's `sess_driver` from files to **Redis**. This allows multiple web servers to share session states seamlessly without file locks.
2. **Read-Write DB Splitting:** Enable CodeIgniter's multiple database group connection feature. Route heavy reporting views (`reports/`) to a Read-Only MySQL replica, leaving the Master DB dedicated to write operations (bookings, badge scanner collection).
3. **Queue Microservice:** Transition the background transactional email cron task (`Cron_email.php`) to a persistent queue manager using **RabbitMQ** or **Redis-Queue (Resque)**. This avoids cron execution lags and ensures instant receipt of tickets/badges by visitors.
