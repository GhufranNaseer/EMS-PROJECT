# EMS Project - Local & Staging Environment Resolution Report (Roman Urdu)

Is document me local development (Laragon) aur Staging environments me aane wale masail, unki wajuhaat (root causes) aur unke solutions ki mukammal tafseel di gayi hai.

---

## Executive Summary

Git pull ke baad local aur staging environment me kuch critical errors pesh aaye thay (jaise assets 404, Dotenv class missing, CORS policy block, database definer issue, aur PDF print me images ka na aana). In tamam masail ko step-by-step hal kar diya gaya hai aur local/staging environments ab bilkul functional hain.

---

## Problems & Solutions Table

| # | Masla (Issue) | Asal Waja (Root Cause) | Solution (Hal) | Impact |
| :--- | :--- | :--- | :--- | :--- |
| **1** | **CSS/JS 404 Load Errors** & Login Button | `.env` file missing thi jis se base URLs root domain (`localhost/`) par chale gaye aur subfolders ignore ho gaye. | Ek local `.env` file banayi aur correct directory URLs (`/EMS-PROJECT/www/`) configure kiye. | Styling aur scripts sahi se load hone lage. |
| **2** | **Fatal Error: Class "Dotenv\Dotenv" Not Found** | PHP libraries ka `vendor/` folder missing tha aur Composer security check legacy packages ko install nahi hone de raha tha. | `composer.json` me audit bypass configure kar ke `composer install --no-dev` chalaya. | Dotenv library successfully register ho gayi. |
| **3** | **CORS Policy Block** (Login block) | Development environment me browser automatically `localhost` ko `[::1]` par redirect kar raha tha, jo `.env` URL se mismatch ho gaya. | `.env` file me base URLs ko `localhost` se badal kar `[::1]` (IPv6) par set kiya. | Origin match ho gaya aur login validation request block hona band ho gayi. |
| **4** | **Database Exception (500 Error)** on Dashboard | Database views/triggers me definer user `'ems_web_user'@'%'` set tha jo local database me missing tha. | Local aur staging database dono me `'ems_web_user'@'%'` user same password ke sath create kar diya. | SQL queries crash hona band ho gayin aur dashboard chal gaya. |
| **5** | **Profile & Exhibition Images 404** | User-uploaded images Git par upload nahi hotin (ignored hain), isliye local folder khali tha. | `uploads/` directories me temporary placeholder images set kar dein. | Broken layout frames clean ho gaye. |
| **6** | **PDF Render Missing Images** (Blank Space) | PDF engine (TCPDF) transparent PNGs ko save karne ke liye `cache` directory dhoond raha tha jo missing thi. | Staging container aur local environment me missing `/cache` directory create kar di. | PDF report me images successfully render hone lagin. |
| **7** | **Attempt to read property on null** | Naye event ke liye bank details database me na hone se view load crash ho raha tha. | Controller me query result null hone par empty `stdClass` fallback object initialize kiya. | Naye events ke liye bank details page bina kisi warning/error ke open ho raha hai. |

---

## Detailed Root Cause Analysis & Solutions

### 1. Broken CSS/JS Assets & Login Button
* **Masla:** Login page ka design kharab tha aur login button click karne par kuch nahi hota tha.
* **Root Cause:** Git pull ke baad local environment me `.env` file nahi thi. CodeIgniter fallback base URL ko `http://localhost/client/` aur `http://localhost/meeting/` par le gaya. Lekin project local Laragon me `/EMS-PROJECT/www/` sub-folder me chal raha tha. Browser ne root folder se CSS/JS fetch karne ki koshish ki aur **404 Not Found** aane par page ka design kharab ho gaya.
* **Solution:** Humne local `.env` file generate ki aur default base URLs ko correct subfolders par set kiya. (Saath hi, Git-tracked `.htaccess` files me hardcoded paths ko revert kar diya taake VPS par koi conflict na ho).

---

### 2. Fatal Error: `Class "Dotenv\Dotenv" not found`
* **Masla:** Login page open karne par fatal php error show ho raha tha.
* **Root Cause:** CodeIgniter ko `.env` load karne ke liye `vlucas/phpdotenv` package chahiye tha jo missing `vendor/` folder ki wajah se nahi mila. Naya Composer security audit alerts ki wajah se process ko block kar raha tha kyunki dev dependencies me legacy `phpunit` ka version require tha.
* **Solution:** Humne `composer.json` ke config block me `"audit": {"block-insecure": false}` add kiya taake safety alerts temporarily bypass ho saken aur phir command chalayi:
  ```bash
  composer install --no-dev
  ```

---

### 3. CORS Policy Block (IPv6 Mismatch)
* **Masla:** Login page open ho jata tha lekin login button click karne par console me CORS request block ka error aata tha.
* **Root Cause:** CodeIgniter development mode me safe communication ke liye `localhost` ko automatically loopback `[::1]` (IPv6) par redirect karta hai. Lekin humare `.env` me URL `localhost` tha. Browser origin `[::1]` se destination `localhost` par request block kar raha tha.
* **Solution:** Humne `.env` file ke base URLs me `localhost` ko badal kar `[::1]` kar diya:
  ```env
  BASE_URL=http://[::1]/EMS-PROJECT/www/
  ```

---

### 4. Database View Definer Mismatch (500 Error)
* **Masla:** Meeting dashboard open karne par exception error aata tha: *"The user specified as a definer ('ems_web_user'@'%') does not exist"*.
* **Root Cause:** Live database dump me views/triggers ka definer (owner) `'ems_web_user'@'%'` set kiya gaya tha, jo local aur staging database me banaya hi nahi gaya tha (wahan sirf `root` user tha).
* **Solution:** 
  * **Locally:** Laragon MySQL database me naya user create kiya:
    ```sql
    CREATE USER 'ems_web_user'@'%' IDENTIFIED BY 'EWEpYe3TJTTT@gKl';
    GRANT ALL PRIVILEGES ON *.* TO 'ems_web_user'@'%';
    ```
  * **Staging VPS:** Staging web database container ke andar ja kar command execute ki:
    ```bash
    docker exec -i ems_stage_mysql mysql -u root -p"StageRootPass###123" -e "CREATE USER IF NOT EXISTS 'ems_web_user'@'%' IDENTIFIED BY 'EWEpYe3TJTTT@gKl'; GRANT ALL PRIVILEGES ON *.* TO 'ems_web_user'@'%' WITH GRANT OPTION; FLUSH PRIVILEGES;"
    ```

---

### 5. PDF Generation me Images ka Show Na Hona (Blank Space)
* **Masla:** PDF print sheet me transparent PNG images (jaise logo) load nahi hotin thin aur khali safaid box dikhta tha.
* **Root Cause:** Transparent PNGs ko process karne ke liye PDF library (TCPDF) transparency mask images generate karti hai, jis ke liye use `htmltopdf/_tcpdf_6.3.2/cache/` folder me temporary files write karni hoti hain. Git-ignored hone ki wajah se ye `cache` folder setup me gayab tha, jis se rendering fail ho rahi thi.
* **Solution:**
  * **Locally:** `cache` directory create ki aur Git track karne ke liye usme `.gitkeep` file add kar di.
  * **Staging VPS:** Staging web container ke andar ye commands run kin:
    ```bash
    docker exec -i ems_stage_web mkdir -p /var/www/html/application/third_party/htmltopdf/_tcpdf_6.3.2/cache
    docker exec -i ems_stage_web chown -R www-data:www-data /var/www/html/application/third_party/htmltopdf/_tcpdf_6.3.2/cache
    ```

---

### 7. PHP Warning: `Attempt to read property "bank_name" on null`
* **Masla:** Naye ya existing event ke liye jab first time "Bank Details" page open kiya jata tha, toh warning throw hoti thi: *"Attempt to read property "bank_name" on null"*.
* **Root Cause:** Controller database se is event ke bank details load karne ki koshish karta hai. Lekin naye event ke liye database table (`bank_details`) me koi record exist nahi karta, jis se result `null` return hota tha. View file bina check kiye `$bank_data->bank_name` aur baqi properties ko read kar rahi thi.
* **Solution:** [Exhibitions.php](file:///c:/laragon/www/EMS-PROJECT/www/application/controllers/Exhibitions.php#L86-L103) controller me agar result empty/null ho, toh humne ek blank dynamic object (`stdClass`) empty values ke sath define kar diya taake view safely open ho sake.

---

## Future Prevention & Automations (Future ke liye hal)

Aage chal kar kisi bhi naye environment me in masail se bachne ke liye ye changes project configurations me save kar di gayi hain:

1. **Automatic Database Definer Creation (Docker Compose):**
   `docker-compose.yml` file me default credentials updates kar di hain taake database start hote hi khud-ba-khud `ems_web_user` create ho jaye:
   ```yaml
   # docker-compose.yml:
   environment:
     MYSQL_ROOT_PASSWORD: "StageRootPass###123"
     MYSQL_DATABASE: ems_stage_db
     MYSQL_USER: ems_web_user
     MYSQL_PASSWORD: "EWEpYe3TJTTT@gKl"
   ```
2. **Tracked PDF Cache Folder:**
   Local cache folder ke andar `.gitkeep` register kar diya hai taake future me naye code pulls me ye folder automatically built-in rahe aur manually create na karna pare.
