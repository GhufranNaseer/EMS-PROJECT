# Quick Start Guide - Run EMS Project Locally
## Complete Step-by-Step Instructions

---

## Overview

This guide will help you run the Exhibition Management System (EMS) project on your computer. It takes about **15-20 minutes** if you follow all steps carefully.

**Requirements:**
- Windows PC (or Mac/Linux with Laragon equivalent)
- Internet connection (for downloads)
- About 500 MB free space

---

## Step 0: What You Have

You downloaded the project from GitHub as a ZIP file.

```
Your File: EMS-PROJECT.zip
```

Now you will:
1. Extract the ZIP
2. Install Laragon
3. Set up database
4. Configure project
5. Run locally

---

## STEP 1: Extract the ZIP File

### What to Do:

```
1. Right-click on EMS-PROJECT.zip
2. Select "Extract All..."
3. Choose location: C:\laragon\www\
4. Click "Extract"
```

### What You Should See:

After extraction, you should have:
```
C:\laragon\www\
└── EMS-PROJECT\
    ├── www/
    ├── docker-compose.yml
    ├── exhibition_system.sql
    └── README.md
```

**Important:** The extracted folder should contain a `www` subfolder inside it.

---

## STEP 2: Install and Start Laragon

Laragon is a local server (like XAMPP). It provides Apache, MySQL, and PHP.

### Download Laragon:

```
Visit: https://laragon.org
Click: Download
```

### Install Laragon:

```
1. Run the installer
2. Click: Next, Next, Next...
3. Click: Install
4. Click: Finish
```

### Start Laragon:

```
1. Open Laragon (double-click the icon)
2. Click: "Start All" button
3. Wait 5-10 seconds
4. Check: Both Apache and MySQL show GREEN
```

If you see green indicators, you are good! ✅

---

## STEP 3: Create Database

### Method A: Using phpMyAdmin (Easiest)

```
1. Open browser
2. Go to: http://localhost/phpmyadmin
3. You should see phpMyAdmin page

4. Click: "New Database"
5. Database name: ems_main_db1
6. Collation: utf8_general_ci
7. Click: "Create"

Done! Database created. ✅
```

### Method B: Using Command Line (Optional)

```
1. Open Terminal (Windows: Press Win+R, type cmd)
2. Run this command:
   mysql -uroot -e "CREATE DATABASE ems_main_db1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
3. Press Enter
4. Done! ✅
```

---

## STEP 4: Import Database File

The project comes with a file: `exhibition_system.sql`
This file contains all tables and sample data.

### Using phpMyAdmin:

```
1. Go to: http://localhost/phpmyadmin
2. Click: Select "ems_main_db1" database (left side)
3. Click: "Import" tab
4. Click: "Choose File" button
5. Select: C:\laragon\www\EMS-PROJECT\exhibition_system.sql
6. Click: "Open"
7. Click: "Go" button
8. Wait... (takes 10-30 seconds)
9. You see green message: "Import successful"

Done! All data is now in database. ✅
```

### Using Command Line (Optional):

```
1. Open Terminal
2. Run this command:
   mysql -uroot ems_main_db1 < C:\laragon\www\EMS-PROJECT\exhibition_system.sql
3. Wait for completion
4. Done! ✅


```
## STEP 4: Update Base Url in Config file (IMP)

Login aur URLs sahi kaam karne ke liye config.php file update karni hogi.

File Location:
C:\laragon\www\EMS-PROJECT\www\application\config\config.php
What to Do:
1. Open File Explorer
2. Navigate to:
   C:\laragon\www\EMS-PROJECT\www\application\config\
3. Open file: config.php
4. Search for:
   $config['base_url']
Replace This Line:

$config['base_url'] = $protocol.$_SERVER['HTTP_HOST'].'/';

With This Line:

$config['base_url'] = $protocol . $_SERVER['HTTP_HOST'] . '/EMS-PROJECT/www/';
Save the File:
1. Press Ctrl + S
2. Close the file

Important: If this URL is not updated correctly, some pages, assets, and login redirects may not work properly.


---

## STEP 6: Fix .htaccess File (VERY IMPORTANT!)

This is the most important step! If you skip this, login will NOT work.

### What to Do:

```
1. Open File Explorer
2. Navigate to: C:\laragon\www\EMS-PROJECT\www\
3. Find file: .htaccess
4. Right-click on .htaccess
5. Select: "Open with" → "Notepad"
```

### What to Change:

Look for line 2. It should say:
```
RewriteBase /
```

Change it to:
```
RewriteBase /EMS-PROJECT/www/
```

### Save the File:

```
1. Press: Ctrl+S
2. Close Notepad
```

**This step is CRITICAL! Without it, login won't work!**

---

## STEP 7: Check Database Credentials

The project has a database configuration file. Let's verify it's correct.

### File Location:

```
C:\laragon\www\EMS-PROJECT\www\application\config\database.php
```

### What Should Be There:

Open the file and find this section:
```php
$db['default'] = array(
    'hostname' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => 'ems_main_db1',
    'dbdriver' => 'mysqli',
```

### Check These Values:

- `hostname`: Should be `127.0.0.1` ✓
- `username`: Should be `root` ✓
- `password`: Should be empty `''` ✓
- `database`: Should be `ems_main_db1` ✓
- `dbdriver`: Should be `mysqli` ✓

If all are correct, you are good! ✅

If you see different password in Laragon, update the password field.

---

## STEP 8: Open Project in Browser

Now let's test if everything works!

### Open Browser:

```
1. Open any browser (Chrome, Firefox, Edge, etc.)
2. Type in address bar: http://localhost/EMS-PROJECT/www/
3. Press Enter
```

### What You Should See:

You should see either:

**Option A:** Login Page
```
- "Login" heading
- Email field
- Password field
- "Login" button
- "Forgot password" link
```

**Option B:** Dashboard (if already logged in from another session)
```
- Project name at top
- User menu on right
- Dashboard content below
```

Both are good! ✅

---

## STEP 9: Test Login

If you see the login page, let's test login.

### Test Credentials:

```
Email:    super.admin@email.com
Password: test123
```

### How to Login:

```
1. Enter email: super.admin@email.com
2. Enter password: test123
3. Click: "Login" button
4. Wait 2-3 seconds...
5. You should see dashboard!
```

### Success Indicators:

```
✅ Loading indicator appeared
✅ Dashboard page loaded
✅ "Super Admin" name shown in user menu
✅ All menu items visible
```

If you see all of these, **congratulations!** Project is running! 🎉

---

## STEP 10: Verify Everything Works

Let's make sure all features are accessible.

### Check These Pages:

```
1. Dashboard: http://localhost/EMS-PROJECT/www/dashboard
   ✓ Should show stats and charts

2. Users List: http://localhost/EMS-PROJECT/www/users.html
   ✓ Should show list of users

3. Create New User: 
   Go to Users → "Add New User"
   ✓ Should show form

4. Exhibitions: (if accessible in your menu)
   ✓ Should show exhibition list
```

If most pages load without errors, your project is working! ✅

---

## Common Problems & Quick Fixes

### Problem 1: "Cannot connect to database"

**Fix:**
```
1. Check if MySQL is running (green indicator in Laragon)
2. Click "Start All" in Laragon again
3. Refresh browser (F5)
```

### Problem 2: "404 Not Found" errors

**Fix:**
```
This means .htaccess is not correct!

1. Open .htaccess file again
2. Check line 2 is exactly: RewriteBase /EMS-PROJECT/www/
3. Save file
4. Refresh browser (Ctrl+Shift+F5 for hard refresh)
```

### Problem 3: Login shows "Invalid login details"

**Fix:**
```
1. Make sure email is: super.admin@email.com
2. Make sure password is: test123
3. Check CAPS LOCK is off
4. Try again
```

### Problem 4: White blank page

**Fix:**
```
1. Open: http://localhost/phpmyadmin
2. If it works, MySQL is fine
3. Check browser console (F12 → Console)
4. Look for error messages
5. Check C:\laragon\www\EMS-PROJECT\www\application\logs\ folder
```

### Problem 5: "RewriteBase" error

**Fix:**
```
This means you missed Step 5!

1. Open .htaccess file
2. Go to line 2
3. Change: RewriteBase /
4. To: RewriteBase /EMS-PROJECT/www/
5. Save
6. Clear browser cache (Ctrl+Shift+Delete)
7. Refresh browser
```

---

## If Something Still Doesn't Work

Try these steps in order:

### Step A: Restart Everything

```
1. Close browser
2. Click "Stop All" in Laragon
3. Wait 5 seconds
4. Click "Start All" in Laragon
5. Wait 10 seconds
6. Open browser again
7. Go to: http://localhost/EMS-PROJECT/www/
```

### Step B: Clear Cache

```
1. Press: Ctrl+Shift+Delete
2. Select: "All time"
3. Check: Cookies, Cache
4. Click: Clear
5. Refresh page (F5)
```

### Step C: Check Logs

```
1. Open: C:\laragon\www\EMS-PROJECT\www\application\logs\
2. Find latest log file
3. Open it
4. Look for error messages
5. Note the error
```

### Step D: Verify Database

```
1. Open: http://localhost/phpmyadmin
2. Click: ems_main_db1
3. Check: Do you see tables like "users", "exhibitions", etc.?
4. If tables are missing, re-import exhibition_system.sql
```

---

## Summary - Quick Reference

| Step | What | Where | Status |
|------|------|-------|--------|
| 1 | Extract ZIP | C:\laragon\www\EMS-PROJECT\ | ✓ |
| 2 | Install Laragon | https://laragon.org | ✓ |
| 3 | Create DB | phpMyAdmin or terminal | ✓ |
| 4 | Import SQL | exhibition_system.sql | ✓ |
| 5 | Fix .htaccess | www\.htaccess | ✓ CRITICAL |
| 6 | Check config | www\application\config\database.php | ✓ |
| 7 | Open browser | http://localhost/EMS-PROJECT/www/ | ✓ |
| 8 | Test login | Email: super.admin@email.com | ✓ |
| 9 | Verify all pages | Check different pages work | ✓ |

---

## What to Do After Setup

### You Can Now:

```
✅ Login to the system
✅ View dashboard
✅ Check user list
✅ Browse different pages
✅ Test features
```

### For Development:

```
✅ Edit files in: C:\laragon\www\EMS-PROJECT\www\
✅ Changes appear in browser immediately
✅ Make backups before big changes
✅ Check logs if something breaks
```

### To Stop the Project:

```
1. Click "Stop All" in Laragon
   (This stops Apache and MySQL)
2. To restart, click "Start All" again
```

---

## Important Paths to Remember

```
Project Folder:
C:\laragon\www\EMS-PROJECT\www\

Key Files:
- .htaccess
- index.php
- application\config\database.php
- application\config\config.php

Browser Access:
- Main: http://localhost/EMS-PROJECT/www/
- phpMyAdmin: http://localhost/phpmyadmin
- Laragon Home: http://localhost
```

---

## Getting Help

If you get stuck:

1. **Check this guide** - Read "Common Problems" section
2. **Check logs** - C:\laragon\www\EMS-PROJECT\www\application\logs\
3. **Check browser console** - Press F12 → Console
4. **Restart Laragon** - Stop All → Start All
5. **Google the error** - Copy error message and search online

---

## System Requirements

Make sure your computer has:

```
✓ Windows 7 or newer
✓ 2GB RAM minimum (4GB recommended)
✓ 500MB free disk space
✓ Internet browser (Chrome, Firefox, Edge, etc.)
✓ Administrator access (to install software)
```

---

## Next Steps

After you get it running:

1. **Explore the system** - Click around, get familiar
2. **Read documentation** - Check README.md for more info
3. **Try features** - Create users, view exhibits, etc.
4. **Ask questions** - If something is unclear

---

## Quick Troubleshooting Checklist

Before asking for help, confirm:

```
□ Laragon shows both Apache and MySQL as GREEN
□ phpMyAdmin is accessible (http://localhost/phpmyadmin)
□ Database "ems_main_db1" exists
□ .htaccess RewriteBase is: /EMS-PROJECT/www/
□ Login credentials are exactly: super.admin@email.com / test123
□ Browser cache has been cleared
□ Tried restarting Laragon
```

If all are checked, project should work! ✅

---

## Contact

If you need help:
- Check this guide again
- Check error logs
- Restart Laragon
- Try on different browser
- Ask your senior/team lead

---

**Document Created:** 2026-06-10
**Version:** 1.0
**Status:** Ready to Use ✅
**Language:** Simple English

---

**You are ready to run the project! Good luck! 🚀**

