# EMS Project - Daily Work & Troubleshooting Report
**Date:** 08 September 2026  
**Author:** DevOps & Engineering Team  

---

## 1. Branch aur Environment Mapping (Architecture)

| Branch | Environment | Live Domain URL | Docker Container (DB) | Database Name |
| :--- | :--- | :--- | :--- | :--- |
| **`developer`** | **Staging (Testing)** | `pms.badarexposolutions.cloud` | `ems_stage_mysql` (Port 3309) | `ems_stage_db` |
| **`main`** | **Production (Live)** | `exhibit.com.pk` | `ems_prod_mysql` (Port 3308) | `ems_main_db` |

---

## 2. Aaj Ke Kaam aur Issues (Step-by-Step Summary)

### Issue 1: Local Environment me Fatal Error (`Dotenv\Dotenv not found`)
* **Wajah:** Local machine par Composer `vendor` folder install nahi tha, jabki `index.php` direct `Dotenv\Dotenv` class call kar raha tha jis se HTTP 500 fatal error aa raha tha.
* **Solution:** Teeno entry files ([`www/index.php`](file:///c:/laragon/www/EMS-PROJECT/www/index.php), [`www/meeting/index.php`](file:///c:/laragon/www/EMS-PROJECT/www/meeting/index.php), [`www/client/index.php`](file:///c:/laragon/www/EMS-PROJECT/www/client/index.php)) me safe fallback add kiya gaya. Ab Composer na hone par bhi `.env` natively load ho jata hai.

---

### Issue 2: phpMyAdmin 400+ MB Database Import Fail / 500 Error
* **Wajah:** phpMyAdmin web-based hai aur PHP ki memory/timeout limit ki wajah se 400+ MB SQL file import karte waqt fail ho jata hai.
* **Solution:** Command line / terminal streaming method use kiya gaya jo 1 minute me bina kisi error ke heavy database import kar deta hai:
  ```powershell
  cmd /c 'mysql -u root ems_stage_db < "C:\laragon\www\EMS-PROJECT\ems_stage_dump_20260908_073312.sql"'
  ```

---

### Issue 3: Exhibition Submit Error (`Unknown column 'networking_fields'`)
* **Error:** `Type: mysqli_sql_exception - Unknown column 'networking_fields' in 'field list'` (Exhibitions.php:241).
* **Investigation:**
  * Staging database (`ems_stage_db`) check kiya: Column `networking_fields` **mojood tha**.
  * Production database (`ems_main_db`) check kiya: Column `networking_fields` **missing tha**.
* **Solution:** Production MySQL container me direct query chala kar missing column add kar diya gaya:
  ```bash
  docker exec -it ems_prod_mysql mysql -u root -p'rUbfOA!WE8XjfyS5' -e "ALTER TABLE ems_main_db.es_exhibitions ADD COLUMN networking_fields TEXT NULL AFTER event_freight_forwarders;"
  ```

---

### 3. Database Comparison & Verification (Audit)
* **Production Tables Count:** 57 tables
* **Staging Tables Count:** 57 tables
* **`diff -u` Check:** Dono databases ki saari 57 tables ke naam 100% match ho chuke hain (Zero difference).

---

### 4. Sidebar Menu Difference Clarification
* **Sawalaat:** Staging par `Email Configuration` aur `Email Logs` menu show ho raha tha lekin Production par nahi.
* **Wazahat:** Yeh code dono branches (`main` aur `developer`) me 100% same mojood hai (Commit `0ba89ae`). Menu ka show hona database ke **User Rights (`hasRight('Emailconfiguration')`)** aur **`SUPER_ADMIN`** role par depend karta hai. Staging database ke user ke paas rights hain jabki production database me us user ko rights assign nahi the.
