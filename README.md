# Exhibition Management System (EMS) - Project Documentation

Welcome to the central repository for the **Exhibition Management System (EMS)**. This project is a comprehensive B2B web application designed to manage, coordinate, schedule, and execute large-scale exhibitions and B2B trade shows. It facilitates bookings, badge printing, space allocations, organizer records, notifications, and contact management for both sales representatives and corporate exhibitors.

---

# 1. Executive Summary

### 1.1 Project Name & Overview
- **Project Name:** Exhibition Management System (EMS)
- **Codebase Path:** `d:\exhibition\exhibition_system\www`
- **Primary Framework:** CodeIgniter 3.1.x (PHP 5.6 to 7.x/8.x compatible)

### 1.2 Project Purpose
EMS is designed to automate and orchestrate the multiple phases of organizing international/local exhibitions (e.g., weapon expos, tech expos, industry trade shows). The system acts as a singular hub for:
1. **Exhibition Planning:** Defining exhibitions, exhibition dates, halls, and physical locations.
2. **Stall Booking & Space Management:** Reserving exhibition spaces, configuring stall builders, and managing packages/pricing.
3. **Attendee & Visitor Facilitation:** Issuing invitation letters, scheduling meetings (B2B, B2G), and managing Trade Visitor badges.
4. **Logistics & Services:** Coordinating freight forwarders, display mobility services, hotels, rental vehicles, and branding options.
5. **Real-time Badge Printing:** Interfacing with badges scanners and managing print queues for attendees.

### 1.3 Business Domain
- **Domain:** B2B Event Management, Corporate Sales, Stall Space Booking, Event Logistics.
- **Audience:** Exhibition organizers, sales representatives, corporate exhibitors, security officials, IT operators.

### 1.4 Main Objectives
- Provide a robust dashboard to track real-time bookings and financial metrics.
- Seamlessly manage complex pricing matrices containing multi-currency costs (PKR, USD) and local taxation.
- Safeguard access using a granular, modular role-permission matrix.
- Ensure reliable performance with an architecture built around custom models (`MY_Model`), Active Record query optimization, and structured logging.

### 1.5 High-Level Architecture
EMS uses the classic MVC (Model-View-Controller) design pattern reinforced by custom core layers (`MY_Controller` and `MY_Model` in `www/application/core/`). Request routing is mapped in `routes.php` to friendly SEO clean URLs (`.html`), which are executed by front-end jQuery AJAX scripts.

---

# 2. Technology Stack Analysis

### 2.1 Backend Engine & PHP Compatibility
- **PHP Version:** Designed for compatibility with PHP versions `>= 5.3.7` up to modern PHP runtimes (e.g. PHP 7.4/8.1, verified using PHP 8.1 CLI during our database testing).
- **PHP Extensions Required:** 
  - `mysqli`: Standard MySQL driver extension.
  - `gd` / `imagick`: Image compression and profile picture processing.
  - `mbstring`: Multibyte string support.
  - `curl`: External integrations (SMS gateways, reCaptcha verify, exchange rates).
  - `zip`: Asset archives.

### 2.2 Framework Architecture
- **Framework:** **CodeIgniter 3.1.6+** (defined in `www/system/core/CodeIgniter.php`).
- **DB Driver:** `mysqli` (Active Record query builder enabled, configured in `www/application/config/database.php`).
- **Sessions:** Session storage is configured using the PHP filesystem driver (`APPPATH . 'cache/'` to ensure Windows/Linux cross-platform persistence).

### 2.3 Database Management
- **Database Engine:** MySQL 8.0 / MariaDB 10.x compatible.
- **Port:** Default local port `3306` (with support for Docker port `3307` mapped inside `docker-compose.yml`).
- **SQL File:** Initial database schema and transactional seeds are contained in `exhibition_system.sql`.

### 2.4 Composer & Third-party PHP Libraries
- **Composer Config (`www/composer.json`):**
  - Dev dependencies: `phpunit/phpunit` (PHP unit testing engine), `mikey179/vfsStream` (virtual file system stream wrapper).
- **Core Custom Libraries:**
  - **`Common.php`** (`www/application/libraries/Common.php`): Custom multi-tool class carrying helper logic for file uploads, reCaptcha validation, HTTP cURL stream queries, and custom error formats.
  - **`PHPMailer`** (`www/PHPMailer`): Custom integration for sending transactional emails (SMTP / local sendmail).
  - **`timthumb.php`** (`www/timthumb.php`): Dynamic on-the-fly image resizing and cropping library.

### 2.5 JavaScript Stack
- **jQuery Engine:** jQuery `2.2.3` and jQuery UI `1.11.4` (loaded in `login.php`, `fp-index.php` and views).
- **Datatables:** jQuery Datatables library (`my_datatable.js`) used for lazy-loaded serverside datatables.
- **Validation Engine:** **`doFormValidation.js`** (`www/assets/js/doFormValidation.js`) — a customized, robust front-end client validation script mapping AJAX inputs to backend database rules.
- **Styling Plugins:** `iCheck` (for sleek, Harmonious checkboxes/radio states), `MorisJS` (chart engines), `SweetAlert` (vibrant alert modals).

### 2.6 CSS Stack
- **Admin Theme:** AdminLTE template.
- **Grid Structure:** Bootstrap CSS grid system.
- **Icons:** FontAwesome v4.7.

---

# 3. Project Structure Analysis

The following directory tree maps the complete visual layout of the **Exhibition Management System (EMS)**:

```
d:\exhibition\exhibition_system\
├── db/                             # Persistent MySQL volumes (Docker-compose)
├── cron/                           # Cron scheduler container configuration
│   └── Dockerfile
├── qr_service/                     # QR-code processing container service
│   └── Dockerfile
├── exhibition_system.sql           # Complete MySQL database schema & seeds
├── Dockerfile                      # Web server PHP + Apache base Dockerfile
├── docker-compose.yml              # Multi-container orchestration config
├── www/                            # Direct public webroot
│   ├── index.php                   # Front controller (CodeIgniter entrypoint)
│   ├── router.php                  # Custom local server URL rewrite engine
│   ├── .htaccess                   # Apache mod_rewrite rule config
│   ├── composer.json               # PHP composer config
│   ├── timthumb.php                # Dynamically resizing script
│   ├── assets/                     # Front-end static assets (JS, CSS, fonts)
│   ├── system/                     # CodeIgniter Framework Core
│   ├── uploads/                    # User uploaded documents, profiles & PDFs
│   ├── client/                     # Client portal sub-application
│   │   ├── application/
│   │   └── index.php
│   ├── meeting/                    # Meeting portal sub-application
│   │   ├── application/
│   │   └── index.php
│   └── application/                # Main Organizers Portal (Core Logic)
│       ├── cache/                  # Session and application logs cache
│       ├── config/                 # Config files (database, routes, configs)
│       ├── controllers/            # Controller layers (endpoints and routing)
│       ├── core/                   # Core controllers (MY_Controller, MY_Model)
│       ├── helpers/                # Application helper functions (Barcode, PDF)
│       ├── libraries/              # Specialized application libraries
│       ├── models/                 # Database Active Record Models
│       └── views/                  # UI Templates (HTML, PHP, layouts)
```

### 3.1 Major Folders Purposes & Relationships

#### 3.1.1 `www/application/`
- **Purpose:** Core backend business logic of the main administration panel.
- **Important Files:** `config/database.php`, `config/routes.php`, `core/MY_Controller.php`.
- **Relationships:** Communicates with MySQL through the `models/` folder, processes dynamic HTTP payloads in `controllers/`, and outputs raw layout bindings through `views/`.

#### 3.1.2 `www/client/` & `www/meeting/`
- **Purpose:** Independent CodeIgniter sub-applications (carrying separate `application/` structures) sharing the same parent database (`ems_main_db`).
- **Relationships:** `client/` manages individual exhibitor profiles and service requests. `meeting/` manages attendee match-making, scheduling agendas, and B2B schedules.

#### 3.1.3 `www/assets/`
- **Purpose:** Stores static assets like vendor JS components, stylesheets, and fonts.
- **Relationships:** Loaded by the View layer of `application/`, `client/`, and `meeting/` to generate rich aesthetics.

#### 3.1.4 `www/uploads/`
- **Purpose:** Storage folder for uploaded profiles, stall mockups, PDF invoices, and requirement documents.
- **Relationships:** Populated by file uploads handled in `Welcome.php` and `portal/`, referenced dynamically in tables and views.

---

# 22. Developer Onboarding Guide

Welcome to the team! Below is the chronological, 1-day onboarding workflow to get you productive on the **Exhibition Management System (EMS)** codebase immediately.

### Phase 1: Environment Bootstrapping (Hour 1 - 2)
1. **Local Setup:** Check your system requirements. Ensure you have PHP 7.4/8.x and MySQL 8.0/MariaDB running (WAMP or native).
2. **Clone & CD:** Locate the project folder at `d:\exhibition\exhibition_system`.
3. **Database Import:** Open your MySQL CLI or phpMyAdmin, create `ems_main_db` and import `exhibition_system.sql`.
4. **Boot Dev Server:**
   ```powershell
   cd d:\exhibition\exhibition_system\www
   php -S localhost:8000 router.php
   ```
5. **Verify Access:** Navigate to `http://localhost:8000` in your browser.

### Phase 2: Login and Navigation (Hour 2 - 3)
1. Log in with the standard developer seed credentials:
   - **Email:** `admin@admin.com`
   - **Password:** `admin123`
2. Open the developer tools (F12) and inspect the network tab.
3. Click through:
   - **Dashboard** (`/dashboard`)
   - **Exhibitions** (`/exhibitions.html`)
   - **Stalls** (`/stalls.html`)

### Phase 3: Architecture Deep-Dive (Hour 3 - 5)
1. Open the file `www/application/core/MY_Controller.php` to understand how session management (`checklogin`), role checking, and custom `_remap` rules operate.
2. Open `www/application/controllers/Welcome.php` to see how AJAX requests are handled in CodeIgniter controllers.
3. Check `www/application/config/routes.php` to see how URLs map to controllers.

### Phase 4: Build Your First Minor Feature (Hour 5 - 8)
1. Task: Try creating a simple test endpoint.
2. Add a new view in `views/test.php` and map it through a custom route inside `config/routes.php`.
3. Make sure to query standard databases using the generic `Usermdl` model layer.
