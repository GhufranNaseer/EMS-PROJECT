# AI Coding Rules & Environment Compatibility Guidelines

This document contains persistent instructions and coding standards for all AI agents working on this repository. Every agent must read and adhere to these guidelines during every task.

---

## 🚨 CRITICAL RULE: Local & VPS (Production) Compatibility

Whenever debugging, writing new features, or fixing errors, **the solution must work seamlessly in both environments**:
1. **Local Development Environment** (e.g., Windows, Laragon, XAMPP, Local Docker)
2. **VPS / Live Production Environment** (e.g., Linux/Ubuntu, Docker containers, Live Nginx)

**Never implement a fix that works in one environment but breaks the other.**

---

## 🛠️ Implementation Standards

### 1. Dynamic Path Resolution & Slash Normalization
* **Rule:** Do not use hardcoded directory separators (`\` or `/`) or hardcoded absolute paths (like `C:\laragon\...` or `/var/www/...`).
* **Slashes:** Always use directory slash normalization, especially on Windows vs. Linux.
* **Practice:**
  * Use PHP constants like `DIRECTORY_SEPARATOR` or normalize paths using `str_replace('\\', '/', $path)`.
  * Use CodeIgniter's `FCPATH` or `APPPATH` dynamically.
  * Always verify that filesystem operations use `is_file()` in addition to `file_exists()` to avoid directory-as-file errors.

### 2. Case Sensitivity (Linux vs. Windows)
* **Rule:** Linux filesystems (VPS) are case-sensitive (e.g., `MyClass.php` is different from `myclass.php`), while Windows (Local) is case-insensitive.
* **Practice:**
  * Always match filenames, controller classes, views, and database tables exactly as they are defined.
  * Avoid issues where code loads successfully on local Windows but fails with a 404/500 error on the Linux VPS due to letter casing mismatches.

### 3. Environment & Configuration Variables
* **Rule:** Keep local and VPS configurations separated using `.env` files or environment variables.
* **Practice:**
  * Use `env()` or `getenv()` helper functions to dynamically load settings (like database hosts, ports, credentials, and base URLs).
  * Never hardcode live production IP addresses, domain names, or credentials directly in source files.

### 4. Database Queries & MySQL Compatibility
* **Rule:** MySQL on Linux is case-sensitive for table names depending on `lower_case_table_names` settings, whereas Windows is generally case-insensitive.
* **Practice:**
  * Ensure table names in SQL queries exactly match the casing in the database schema (e.g., `es_officer` vs `ES_OFFICER`).

---

## 🎯 Verification Checklist Before Completing Work

Before concluding any task, verify:
* [ ] Does this code rely on Windows-specific or Linux-specific paths? (If yes, make it dynamic).
* [ ] Are all path slashes handled safely for both `\` (Windows) and `/` (Linux)?
* [ ] Are class/file loads case-sensitive safe?
* [ ] Have changes been tested or verified conceptually to ensure no live production environment variables/ports are conflicted?
