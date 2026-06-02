# Exhibition Management System (EMS) - Technical Documentation

This document contains exhaustive codebases, modular features, role metrics, complete routing matrices, database parameters, API specifications, and controller-model inventories for the **Exhibition Management System (EMS)**.

---

# 5. Feature Inventory

EMS contains a comprehensive set of operational modules. Below is the inventory of core system features:

### 5.1 User Authentication & Authorization
- **Purpose:** Securely identify users and dynamically restrict portal menus based on rights.
- **Files Involved:** `Welcome.php` (controller), `views/login.php`, `core/MY_Controller.php` (middleware).
- **Database Tables Used:** `users`, `usergroup`.
- **User Roles Involved:** All Roles.
- **Flow Description:** User inputs email and password on the login screen. Front-end AJAX validates with `/login-validate` to check database credentials. If successful, the form natively submits to `/login-submit` to establish session parameters and redirect the user to the default homepage (`dashboard`).

### 5.2 Dynamic Stall Space Booking
- **Purpose:** Allocating specific physical exhibition spaces to corporate exhibitors.
- **Files Involved:** `Book_stall.php` (controller), `models/Funcs.php`.
- **Database Tables Used:** `es_exhibition_booking`, `es_exhibition_stalls`, `es_exhibition_booking_stalls`.
- **User Roles Involved:** Corporate Exhibitors, Sales Persons, Managers.
- **Flow Description:** The sales representative selects an active exhibition, fetches the live visual layout of halls, allocates a specific stall category (Standard, Shell, Premium), sets currency rates (USD/PKR), applies tax rules, and updates the stall reservation state.

### 5.3 Attendee Badge Scanning & Printing
- **Purpose:** Real-time generation of security badges during live exhibition check-ins.
- **Files Involved:** `Badges_scan.php`, `Print_job.php` (controllers).
- **Database Tables Used:** `es_exhibition_badges`, `es_print_job`, `user_log`.
- **User Roles Involved:** Facilitation Staff, IT Department, Attendees.
- **Flow Description:** The staff member scans a visitor's QR code at the check-in counter. The system fetches the visitor record, generates a print job containing badge layouts (names, companies, barcodes), and appends it to `es_print_job` for local hardware printers.

---

# 6. User Roles & Permissions

EMS uses a group-based Access Control List (ACL). Individual controller actions check the `usergroup_rights` of the active session.

### 6.1 Role Definitions
- **Administrador (ID 1):** Full System Authority. Holds wildcard `*` permissions across all dynamic controllers and system settings.
- **Customer (ID 2):** Exhibitor portal access. Restricted to viewing their own bookings, billing, and profile.
- **Sales Person (ID 3):** Handles bookings and invoicing. Permissions restricted to `Book_stall.php` and `Order_list.php`.
- **Manager (ID 4):** Operations lead. Holds administrative review rights and is restricted to `Order_list.php`.
- **Facilitation Staff (ID 6):** Handles onsite check-ins, layouts, and print tasks. Allowed controllers: `Settings.php`, `Stalls.php`, `Event_inventory.php`, `Customers.php`, `Contact_person.php`, `Book_stall.php`, `Packages.php`, `Inventory_category.php`, `Inventory_item.php`.

### 6.2 Role Permission Matrix

| Controller Name | Administrador | Customer | Sales Person | Manager | Facilitation |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **`Welcome.php`** (Core) | ✅ | ✅ | ✅ | ✅ | ✅ |
| **`Portal.php`** (Dashboard) | ✅ | ✅ | ✅ | ✅ | ✅ |
| **`Book_stall.php`** | ✅ | ❌ | ✅ | ❌ | ✅ |
| **`Order_list.php`** | ✅ | ❌ | ✅ | ✅ | ❌ |
| **`Stalls.php`** | ✅ | ❌ | ❌ | ❌ | ✅ |
| **`Settings.php`** | ✅ | ❌ | ❌ | ❌ | ✅ |

---

# 7. Database Documentation

### 7.1 Database Overview
- **Database Name:** `ems_main_db`
- **Total Operational Tables:** 49
- **Collation:** `latin1_swedish_ci` / `utf8_general_ci`

### 7.2 Core Tables Schema

#### 7.2.1 `users`
- **Purpose:** Holds all platform users, salt hashes, status keys, and specific access overrides.
- **Columns:**
  - `id` (int, PK, Auto-Increment)
  - `user_group_id` (int, FK referencing `usergroup.id`)
  - `user_first_name` (varchar)
  - `user_last_name` (varchar)
  - `user_email` (varchar, Unique)
  - `user_password` (varchar, MD5 Hash)
  - `is_active` (tinyint)
  - `user_rights` (text)

#### 7.2.2 `es_exhibitions`
- **Purpose:** Stores the high-level schedules and branding definitions for expos.
- **Columns:**
  - `id` (int, PK)
  - `exhibition_title` (varchar)
  - `exhibition_logo` (varchar)
  - `status` (tinyint)
  - `created_on` (datetime)

#### 7.2.3 `user_log`
- **Purpose:** Holds transactional database mutation history for administrative auditing.
- **Columns:**
  - `id` (int, PK)
  - `user_id` (int)
  - `log_query` (text)
  - `ip_address` (varchar)
  - `createdon` (datetime)

### 7.3 Integrity & Performance Analysis
- **Missing Indexes:** Heavy search tables such as `es_exhibition_booking` and `es_print_job` frequently filter by `exhibition_id` or `status` but lack composite B-Tree indexes. Add index `idx_booking_exh_status` on `(exhibition_id, status)` to boost performance.
- **Storage Engine:** The logging table `user_log` uses MyISAM. MyISAM uses table-level locking. For concurrent checks, convert `user_log` to InnoDB to enable row-level locking.

---

# 8. Routes Documentation

The routing mappings are configured inside `www/application/config/routes.php`. Below is the complete route catalog:

| URL Endpoint | Request Method | Controller/Function | Purpose | Authentication |
| :--- | :---: | :--- | :--- | :---: |
| `/login` | `GET` | `welcome/login` | Renders the primary login screen | Public |
| `/login-validate` | `POST` | `welcome/login_validate/doError` | Validates email and credentials | Public |
| `/login-submit` | `POST` | `welcome/login_submit` | Sets up sessions and logs user in | Public |
| `/dashboard` | `GET` | `portal` | Renders dynamic operations dashboard | Required (`@`) |
| `/exhibitions.html` | `GET` | `exhibitions/crd_list` | Lists all structured exhibitions | Required (`@`) |
| `/stalls.html` | `GET` | `stalls/crd_list` | Manages hall spaces and categories | Required (`@`) |
| `/log-out` | `GET` | `welcome/logout` | Clears sessions and deletes cookies | Required (`@`) |

---

# 9. Controller Documentation

### 9.1 `Welcome.php`
- **Purpose:** Handles pre-authentication, logins, recover tokens, passwords update, and background emails.
- **Methods:**
  - `login()`: Renders `views/login.php`.
  - `login_validate()`: Evaluates inputs, queries MD5 hashes, and exits.
  - `login_submit()`: Sets up persistent cookies and redirects to the home page.
  - `logout()`: Destroys session hashes.
- **Dependencies:** `Usermdl`, `Common.php`.

### 9.2 `Portal.php`
- **Purpose:** Dashboard view generator. Queries and renders aggregated graphs of bookings, transaction logs, and attendees check-ins.
- **Database Tables Used:** `es_exhibition_booking`, `es_customers`, `user_log`.

---

# 10. Model Documentation

### 10.1 `Usermdl.php`
- **Purpose:** Performs operations on the `users` table and dynamically parses controller scripts in the directory to present custom access selection options.
- **Methods:**
  - `getAllRights()`: Loops through files inside `application/controllers` using the native `RecursiveIteratorIterator` to generate a dynamic rights checklist.
  - `getUserImage()`: Resolves user profile avatar path or defaults to `default.png`.

### 10.2 `Tem_vals.php`
- **Purpose:** Key-value manager utilizing the database table `temp_value`. It serializes and base64-encodes values for secure email-bound links (e.g. forgot password recover tokens) and implements built-in garbage collection during instantiation.

---

# 11. API Documentation

EMS contains a RESTful API sub-module inside the `www/application/controllers/apis/` directory.

### 11.1 Badge Verification API
- **Endpoint:** `POST /apis/services/verify_badge`
- **Headers:** `Content-Type: application/json`
- **Request Parameters:**
  ```json
  {
    "badge_code": "EXH-99827-2026"
  }
  ```
- **Response Format:**
  - **Success (`200 OK`):**
    ```json
    {
      "status": "success",
      "data": {
        "visitor_name": "John Doe",
        "company": "Tech Corp",
        "status": "Verified"
      }
    }
    ```
  - **Fail (`404 Not Found`):**
    ```json
    {
      "status": "error",
      "message": "Invalid badge code or visitor not registered"
    }
    ```

---

# 21. Modification Impact Map

Use this map to verify the downstream dependencies before modifying code:

```
┌────────────────────────┐
│   Database Schema      │
│  (e.g., es_customers)  │
└───────────┬────────────┘
            │
            ▼
┌────────────────────────┐
│      Model Layer       │
│  (e.g., Usermdl.php)   │
└───────────┬────────────┘
            │
            ▼
┌────────────────────────┐
│    Controller Actions  │
│  (e.g., Stalls.php)    │
└───────────┬────────────┘
            │
            ▼
┌────────────────────────┐
│     AJAX / JS Validation│
│ (doFormValidation.js)  │
└───────────┬────────────┘
            │
            ▼
┌────────────────────────┐
│      View Layer        │
│   (e.g., login.php)    │
└────────────────────────┘
```

#### **Database Modification Impact:**
If you rename or alter columns inside `users` or `es_exhibition_booking`:
- **Files to check:** `application/core/MY_Controller.php` (specifically `varifyUser` and `getUsernamePasswordWithRme`), as raw SELECT and CONCAT queries will break if database columns mismatch.
- **UI Impact:** Verify input parameters in `views/login.php` to ensure the name attributes match the newly modified database columns.
