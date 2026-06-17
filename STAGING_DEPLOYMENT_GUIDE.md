# Exhibition Management System (EMS) - Staging Deployment & Operations Guide

Yeh guide EMS project ko VPS server par ek isolated **Staging (Stage) Version** ke taur par lagane, use safely run karne, aur local changes ko live staging site par sync karne ke mukammal aur deep steps faraham karti hai.

---

## 1. Project & VPS ki Current Situation

1. **Production (Live) Environment:**
   - Active system VPS par `/var/www/exhibition_system_prod` directory me run ho raha hai (Project name: `ems_prod`).
   - Ports: **8002 (Web)**, **8003 (QR)**, aur **3308 (MySQL)**.
   - Domain: `https://exhibit.com.pk/` (Nginx reverse proxy with SSL).
2. **Legacy System Decommissioned:**
   - Purana system `/var/www/html/exhibition_system` (ports 8000/8001/3307) stop aur delete ho chuka hai jis se disk space free ho gayi hai.

---

## 2. Staging Deployment - Step-by-Step Instructions

Staging setup ko hum direct production ke sath baghair kisi conflict ke install karenge.

### Step 1: Directory Setup (Filesystem Separation)
VPS par ja kar staging codebase ke liye ek naya folder banayenge aur target branch (develop) clone karenge.
```bash
# Git se develop/staging branch ko naye stage folder me clone karne ke liye
sudo git clone -b develop https://github.com/GhufranNaseer/EMS-PROJECT.git /var/www/exhibition_system_stage
```
* **Kyu kar rahe hain (Why):** Production aur staging ka source code bilkul isolated hona chahiye. Agar hum ek hi folder me files edit karenge to live production site crash ho sakti hai.

---

### Step 2: Permissions Configuration
Web server (Nginx/Apache) aur PHP processes ko files/folders write karne ki permission dena.
```bash
sudo chown -R kamran:www-data /var/www/exhibition_system_stage
sudo chmod -R 775 /var/www/exhibition_system_stage
sudo chmod -R 777 /var/www/exhibition_system_stage/www/application/cache
sudo chmod -R 777 /var/www/exhibition_system_stage/www/uploads
```
* **Kyu kar rahe hain (Why):** PHP framework ko local templates cache karne aur users ko validation certificates/profile pictures upload karne ke liye directory write rights chahiye hote hain. `www-data` group web server ki access ensure karta hai.

---

### Step 3: Staging Environment File Config (`.env`)
Staging database credentials aur mode set karne ke liye alag configuration file set karna.
* **File path VPS par:** `/var/www/exhibition_system_stage/www/.env`
* **Content:**
```ini
ENVIRONMENT=development
ENCRYPTION_KEY=Kx3piZIuin5He31fGN9elUk8fn7bKCMm_stage
LOG_THRESHOLD=2

# Note: CodeIgniter 3 strictly supports: 'development', 'testing', or 'production' only.
# Hum staging environment par bugs debug karne ke liye 'development' set kar rahe hain.

# Database settings pointing to staging mysql service container name
DB_HOST=ems_stage_mysql
DB_USER=ems_stage_user
DB_PASS=StagePass###123
DB_NAME=ems_stage_db
DB_DRIVER=mysqli
```
* **Kyu kar rahe hain (Why):** Environment variables key/secrets ko codebase se door rakhte hain. Is config se staging application production database se connect nahi hogi balkey apne isolated staging container database se connect karegi.

---

### Step 4: Staging Docker Compose Configuration
Staging ke liye specific container services aur unique ports configuration define karna.
* **File path VPS par:** `/var/www/exhibition_system_stage/docker-compose.yml`
* **Content:**
```yaml
version: '3.9'
services:
  webserver:
    container_name: ems_stage_web
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - ./www:/var/www/html
    ports:
      - "8004:80"             # Staging web interface (Host port 8004 -> Container port 80)
    depends_on:
      - mysql-db
    networks:
      - ems_stage_network

  mysql-db:
    container_name: ems_stage_mysql
    image: mysql:8.0
    command: --default-authentication-plugin=mysql_native_password
    volumes:
      - ems_stage_db:/var/lib/mysql
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: "StageRootPass###123"
      MYSQL_DATABASE: ems_stage_db
      MYSQL_USER: ems_stage_user
      MYSQL_PASSWORD: "StagePass###123"
    ports:
      - "3309:3306"           # Staging database (Host port 3309 -> Container port 3306)
    networks:
      - ems_stage_network

  cronjob:
    container_name: ems_stage_cron
    build:
      context: .
      dockerfile: cron/Dockerfile
    depends_on:
      - webserver
    networks:
      - ems_stage_network

  ems_qr_service:
    container_name: ems_stage_qr
    build:
      context: .
      dockerfile: qr_service/Dockerfile
    ports:
      - "8005:8000"           # Staging QR service (Host port 8005 -> Container port 8000)
    depends_on:
      - webserver
    networks:
      - ems_stage_network

networks:
  ems_stage_network:
    driver: bridge

volumes:
  ems_stage_db:
```
* **Kyu kar rahe hain (Why):**
  * **Ports 8004, 8005, aur 3309:** Production ports (8002, 8003, 3308) se clash hone se bachati hain.
  * **Unique Container & Network Names:** Docker engine me containers conflict ko rokti hain.
  * **Volume Persistence:** Database files ko staging volume me isolated aur safe rakhti hain.

---

### Step 5: Staging Containers Build & Run
Staging server ko run state me lana.
```bash
cd /var/www/exhibition_system_stage
docker compose -p ems_stage up -d --build
```
* **Kyu kar rahe hain (Why):** `-p ems_stage` docker project name override hai taake production image overlap na ho. `--build` command new image compose karti hai aur `-d` back-ground process execute karta hai.

---

### Step 6: Database Setup aur Import
Initial tables populate karne ke liye dump import karna.
1. Database file check karein ke isme koi active command `USE exhibition_system;` ya similar script runtime database block to nahi kar rahi. Agar hai to use delete karein taake setup automatically `ems_stage_db` default target database select kare.
2. SQL database file staging mysql container ke andar import karein:
   ```bash
   docker exec -i ems_stage_mysql mysql -u ems_stage_user -p"StagePass###123" ems_stage_db < /var/www/exhibition_system_stage/exhibition_system.sql
   ```
* **Kyu kar rahe hain (Why):** `docker exec -i` container ke terminal shell me binary run karta hai. Staging local database space ko raw structure se build kar deta hai.

---

### Step 7: Nginx Configuration for Staging Subdomain
User validation ke liye unique staging domain setup proxy add karna.
1. Site active virtual host file create karein:
   ```bash
   sudo nano /etc/nginx/sites-available/ems_stage
   ```
2. Configuration script add karein:
   ```nginx
   server {
       listen 80;
       server_name stage.exhibit.com.pk;

       location / {
           proxy_pass http://127.0.0.1:8004;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
           proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
           proxy_set_header X-Forwarded-Proto $scheme;
       }
   }
   ```
3. Link and reload:
   ```bash
   sudo ln -s /etc/nginx/sites-available/ems_stage /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo nginx -s reload
   ```
* **Kyu kar rahe hain (Why):** Browser requests (Port 80/HTTP) ko Nginx internal port `8004` par shift karta hai taake subdomain standard user end par display ho sake. `nginx -s reload` zero downtime reload karta hai.

---

### Step 8: SSL Let's Encrypt Certificate
Domain ko secure HTTPS par shift karna.
```bash
sudo certbot --nginx -d stage.exhibit.com.pk --non-interactive --agree-tos --email ghufran.naseer0987@gmail.com --redirect
```
* **Kyu kar rahe hain (Why):** HTTPS verify na hone se assets (CSS/JS files) render nahi hote aur form requests secure transmit nahi hoti hain (ERR_SSL_PROTOCOL_ERROR prevent karne ke liye).

---

## 3. Development & Update Workflow (Local changes VPS me kaise show hongi?)

Aapka ahem sawal: **"Github se clone karne ke baad, jab local computer par changes karenge to wo VPS par kaise show hongi?"**

Iske liye **2 mukhtalif tarike** use hote hain. Aap apni pasand ke mutabiq chun sakte hain:

### Option A: Professional Git Workflow (Recommended)
Agar aap regular changes karte hain aur clean history rakhna chahte hain:

```
[Local Machine] ───(git commit & push)───► [GitHub (develop branch)]
                                                      │
                                                 (git pull)
                                                      ▼
                                                [VPS Server]
```

1. **Local changes commit aur push karein:**
   Jab local system par code change kar lein, to use commit kar ke GitHub par push karein:
   ```bash
   git add .
   git commit -m "fix: login dynamic redirect"
   git push origin develop
   ```
2. **VPS par changes pull karein:**
   VPS login karein aur staging target directory me ja kar latest branch pull karein:
   ```bash
   cd /var/www/exhibition_system_stage
   git pull origin develop
   ```
3. **If needed, rebuild/restart containers:**
   - **Case 1: Sirf PHP/HTML views edit hue hain:** Aapko kuch karne ki zaroori nahi hai! Docker settings me humne `./www:/var/www/html` volume bind kiya hua hai. Git pull hote hi browser me changes auto reflect ho jayengi.
   - **Case 2: Dockerfile ya dependencies (Composer packages) change hui hain:**
     Aapko container reload karna hoga:
     ```bash
     docker compose -p ems_stage up -d --build
     ```

---

### Option B: Fast Direct Sync (No Git / SCP Workflow)
Agar aap code GitHub par push nahi karna chahte aur direct test karna chahte hain:

1. **Local machine se direct files upload karein:**
   Aap local system ke terminal (PowerShell/CMD) ya kisi SFTP tool (jaise FileZilla / WinSCP / VS Code extension) ke zariye direct target file upload kar sakte hain:
   ```powershell
   # PowerShell local terminal se (sirf specific changed file send karne ke liye):
   pscp -pw Hosting###123 C:\laragon\www\EMS-PROJECT\www\application\controllers\Welcome.php kamran@184.168.125.171:/var/www/exhibition_system_stage/www/application/controllers/
   ```
2. **Result Check:**
   File upload hote hi volume mapping ke zariye code browser me foran chal jayega.

---

### Option C: Database Sync Workflow
Agar aap ne local database me changes (jaise tables create ya update) kiye hain:

1. **Local DB dump export karein:**
   Local system par: `mysqldump -u root ems_main_db1 > dump.sql`
2. **VPS par copy karein:**
   ```powershell
   pscp -pw Hosting###123 dump.sql kamran@184.168.125.171:/var/www/exhibition_system_stage/
   ```
3. **Import to Staging MySQL container:**
   ```bash
   docker exec -i ems_stage_mysql mysql -u ems_stage_user -p"StagePass###123" ems_stage_db < /var/www/exhibition_system_stage/dump.sql
   ```
