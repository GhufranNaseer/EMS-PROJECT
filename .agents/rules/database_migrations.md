---
trigger: always_on
---

# Automated Database Migration Protocol (Mandatory)

Whenever the user requests a feature, fix, or update that involves creating a new database table, altering an existing table (adding/modifying columns or indexes), or inserting initial seed data:

1. **Do NOT ask the user to manually create the migration or run SQL queries.** Handle it 100% automatically like Laravel's migration flow.
2. **Step 1 - Create Migration File:**
   - Create a new migration file inside `db/migrations/` using timestamp format:
     `YYYYMMDD_HHMMSS_action_name.sql` (e.g. `20260911_120000_create_meeting_notes.sql`).
   - Use safe, conflict-free syntax:
     - Always use `CREATE TABLE IF NOT EXISTS`.
     - Never use `DROP TABLE` or `TRUNCATE TABLE`.
3. **Step 2 - Apply Locally:**
   - Immediately execute the migration locally using:
     `php db/migrate.php`
   - Verify that the command succeeds with `[SUCCESS]` and logs in `es_migrations`.
4. **Step 3 - Proceed with Application Code:**
   - Build the CodeIgniter models, controllers, and views that utilize the table.
5. **Step 4 - Deployment Preparedness:**
   - Ensure the `.sql` migration file in `db/migrations/` is tracked and committed alongside the application code.
   - The GitHub Actions production deploy workflow (`.github/workflows/deploy-prod.yml`) will automatically run `php db/migrate.php` upon deployment to VPS.
