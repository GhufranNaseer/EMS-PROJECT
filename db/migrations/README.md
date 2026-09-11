# EMS Database Migrations (Conflict-Free Architecture)

This directory contains incremental, version-controlled database schema and data migration files.

## 🔒 Golden Rules for Conflict-Free Live Deployments

Since this project has been live in production for a long time, **all migrations must strictly adhere to the following rules**:

1. **Never Drop or Truncate Existing Tables:**
   - ❌ `DROP TABLE ...`
   - ❌ `TRUNCATE TABLE ...`
   - ❌ `ALTER TABLE ... DROP COLUMN ...` (unless explicitly requested and verified)

2. **Always Use Idempotent / Safe Syntax:**
   - For new tables:
     ```sql
     CREATE TABLE IF NOT EXISTS `es_your_table` (
         `id` INT(11) NOT NULL AUTO_INCREMENT,
         ...
         PRIMARY KEY (`id`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
     ```
   - For adding columns to existing tables:
     Use safe ALTER TABLE statements or check column existence.

3. **File Naming Convention:**
   Files must follow natural chronological order:
   `YYYYMMDD_NNN_descriptive_name.sql`
   - Example: `20260911_001_create_meeting_schedules.sql`
   - Example: `20260915_002_add_hall_color_column.sql`

4. **How Migrations are Run:**
   - **Local Development:**
     ```bash
     php db/migrate.php
     ```
   - **Check Status:**
     ```bash
     php db/migrate.php --status
     ```
   - **Live Production / VPS Deployment:**
     Integrated directly into GitHub Actions (`.github/workflows/deploy-prod.yml`).
     When new code is pushed, only unexecuted migrations are applied. Migrations that have already run are automatically skipped.
