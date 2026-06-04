# Migration Project Plan: Filesystem Assets Migration

This document outlines the formal plan for auditing and migrating filesystem media assets (uploads, images, documents, attachments, and media files) from the OLD SYSTEM (`exhibition_system`) to the NEW SYSTEM (`exhibition_system_prod`).

---

## 1. Executive Summary

*   **Purpose of Migration**: To transition all historical and active media assets from the OLD SYSTEM to the NEW SYSTEM. This migration is a one-way consolidation, aiming to populate the NEW SYSTEM with all necessary filesystem assets so it can run fully independently.
*   **Current Architecture**:
    *   **OLD SYSTEM (exhibition_system - Legacy Source)**: Legacy application codebase, legacy uploads, and legacy database storage directories.
    *   **NEW SYSTEM (exhibition_system_prod - Production Target)**: Newly deployed production environment running on port `8002` using the `ems_prod_web` container.
*   **Target Architecture**: A fully self-contained NEW SYSTEM (`exhibition_system_prod`) hosting its own local database (`ems_main_db`) and resolving all media assets directly from its local filesystem directory without any symbolic links, network mounts, or dependency on the OLD SYSTEM path.
*   **Expected Outcome**: Complete operational independence of the NEW SYSTEM (`exhibition_system_prod`), leading to the eventual safe decommissioning and retirement of the OLD SYSTEM (`exhibition_system`).

---

## 2. Existing Environment Analysis

### 2.1 Legacy Directory Verification (Critical Findings)
The OLD SYSTEM paths are distinct, independent directories on the host that serve completely different functions. They are not identical and are not synchronized:
1.  **`/home/kamran/exhibition_system`**:
    *   **Owner**: `root:root` (with subdirectory `db` owned by `lxd:root`).
    *   **Purpose**: Legacy database storage. This directory contains only raw InnoDB/MySQL database storage files (e.g., `ibdata1`, tablespace files, binlogs) for the legacy database container.
    *   **Constraint**: This directory does not contain any application source code, codebase files, or the `uploads` directory.
2.  **`/var/www/html/exhibition_system`**:
    *   **Owner**: `kamran:kamran`.
    *   **Purpose**: Codebase and uploads location. This directory contains the application codebase (`www/`) and the historical `uploads/` folder.
    *   **Constraint**: This is the actual source for all filesystem assets, media files, and uploads.

### 2.2 Target Environment
*   **NEW SYSTEM Target Path**: `/var/www/exhibition_system_prod/` on the VPS host.
*   **NEW SYSTEM Database**: Staging database `ems_main_db` running on port `3308` (active MySQL container `ems_prod_mysql`).

### 2.3 Uploads Folder Structure
Inside the OLD SYSTEM (`/var/www/html/exhibition_system/www/uploads/`) and NEW SYSTEM (`/var/www/exhibition_system_prod/www/uploads/`):
*   `badge_cnic/` (Visitor/exhibitor CNIC and passport scans/documents).
*   `exhibition/` (Exhibition and event branding logos/banners).
*   `profile/` (User, administrator, and officer profile pictures).
*   `qr-codes/` (SVGs, barcode files, and print badge artifacts).
*   `temp/` (Temporary upload files).

---

## 3. Risk Assessment

*   **Potential Risks**:
    1.  **Orphaned Assets/Missing Files after Retirement**: Files referenced in the database that are omitted during migration will break features in the NEW SYSTEM once the OLD SYSTEM directory is retired or deleted.
    2.  **Disk Space Exhaustion**: The OLD SYSTEM `uploads/` tree is ~37GB, whereas the host disk `/dev/sda2` has only ~32GB of free space. A brute-force copy of the entire uploads directory will fill the disk, causing container crashes and service downtime.
    3.  **Permission/Ownership Lockout**: Files copied without correcting ownership to the running webserver group (`www-data`) will not be readable/writable by the application.
    4.  **Overwrite of Target Data**: Accidental replacement of active NEW SYSTEM uploads with legacy files.
*   **Safety Policies**:
    *   **NO Data Loss Policy**: All existing files on both source and target must be preserved.
    *   **NO Automatic Deletion Policy**: No files or folders in either system will be automatically deleted by any script or sync operation.
    *   **NO Blind Overwrite Policy**: Target files will never be overwritten blindly. All file copies must use non-destructive checks.
    *   **NO Production Downtime Policy**: All procedures must run alongside active containers, with zero downtime to the running application.

---

## 4. Uploads Audit Strategy

*   **Directory Comparison Methodology**:
    *   Execute recursive file counts, folder lists, and sizing checks for all subdirectories (`badge_cnic`, `exhibition`, `profile`, `qr-codes`, `temp`).
*   **File Comparison Methodology**:
    *   Generate a list of files (with path and size/checksums) in both projects.
*   **Missing File Detection Methodology**:
    *   Compare filenames in the OLD SYSTEM (`/var/www/html/exhibition_system/www/uploads/`) against the NEW SYSTEM (`/var/www/exhibition_system_prod/www/uploads/`) to isolate files that exist only in the legacy system.
*   **Sample-Based Verification (Pre-Migration Validation)**:
    *   Before executing any file copying, select a random sample of 50–100 database-referenced files from the list of files to migrate.
    *   Confirm that these files physically exist in the OLD SYSTEM.
    *   Confirm that these files are missing in the NEW SYSTEM.
    *   Document the results of this manual/scripted validation check to confirm path patterns and integrity before moving past the analysis phase.
*   **Verification Methodology**:
    *   Compile a final audit report showing:
        *   Files existing in the OLD SYSTEM only.
        *   Files successfully copied.
        *   Files remaining missing or un-migrated.
        *   Unresolved errors or conflicts.

---

## 5. Folder Mapping Plan

*   **OLD SYSTEM Paths (Source)**:
    *   `/var/www/html/exhibition_system/www/uploads/`
*   **NEW SYSTEM Paths (Target)**:
    *   `/var/www/exhibition_system_prod/www/uploads/`
*   **Mapping Principle**: Complete directory-to-directory structure mirroring, ensuring filenames and folder hierarchies align perfectly.

---

## 6. Database Reference Verification Strategy

*   **Database Extraction**:
    *   Query the NEW SYSTEM database (`ems_main_db`) to select all fields in all tables containing file/image references (e.g., `users.user_image`, `es_exhibitions.event_logo`, `es_exhibitions.associate_logo`, `es_organizer.organizer_image`, `es_exhibition_badges.barcode_data`, and document attachments).
*   **Asset Validation Process**:
    *   Cross-reference the resulting database filename list against the OLD SYSTEM directory and the NEW SYSTEM directory.
    *   Determine if any database-referenced files are completely missing from both filesystems (unresolvable database orphans) or if they exist in the OLD SYSTEM but are missing from the NEW SYSTEM.

---

## 7. Migration Strategy

1.  **Audit Phase**: Run non-destructive inspection scripts to index files, check disk usage, and query database references.
2.  **Validation Phase**: Filter the list of missing files to target *only* required files (those referenced in the database or active folders), resolving the storage limitation risk.
3.  **Sample-Based Verification Phase**: Randomly select 50–100 DB-referenced files, and verify that they are present in the OLD system and missing in the NEW system.
4.  **Mandatory Dry Run Phase**: Run `rsync` with the `--dry-run` option to verify the exact file list and verify the operations before executing any real changes.
5.  **Approval Hold Phase**: The project must remain in the "analysis phase". No copying, deleting, or overwriting operations should be executed without explicit, final manual approval from the user.
6.  **Batch Copy Phase**: Upon receiving explicit user approval, execute copying of the approved subset of files in small, controlled batches (not the entire 27GB+ dataset at once) to validate behavior, ensure stability, and monitor disk space. Log all operations in a migration manifest.
7.  **Verification Phase**: Validate file existence and permissions in the target container. Test print and view dashboards.
8.  **Final Approval Phase**: Present the complete audit report to the user for formal sign-off.

---

## 8. Safety Controls

*   **Approval Hold (Strict Constraint)**: The system must remain strictly in the "analysis phase". No file copy, delete, or overwrite operations are to be performed without explicit, prior manual approval from the user.
*   **Mandatory Dry Run**: A dry run using `rsync --dry-run` must be run and verified first before any real changes are executed to review the precise list of proposed file transfers.
*   **Batch Migration Approach**: A direct, monolithic migration of the full 27GB+ dataset is strictly prohibited due to disk capacity boundaries and stability risk. Files must be migrated in small, sequential batches, validating system behavior and disk space after each batch.
*   **No Overwrite Policy**: All copy commands must use flags (like `cp -n` or `rsync --ignore-existing`) to prevent overwriting assets in the NEW SYSTEM.
*   **No Delete Policy**: Do not delete, move, or rename any files in either system during the audit or copy phases.
*   **No Downtime Policy**: Do not restart Nginx (only reload) or stop any containers.
*   **No Production Impact Policy**: Access legacy directories only in a read-only manner, keeping all active services untouched.

---

## 9. Commands To Be Used

*   **Inspection Commands Only**:
    *   `df -h` (Verify disk space)
    *   `du -sh [directory]` (Verify directory size)
*   **Safe Comparison Commands**:
    *   `find /var/www/html/exhibition_system/www/uploads -type f` (List source files)
    *   `find /var/www/exhibition_system_prod/www/uploads -type f` (List target files)
*   **Mandatory Dry Run Command**:
    *   `rsync -aunv --ignore-existing [source] [target]` (Perform dry run to preview exactly what files will be copied without making actual changes)
*   **Batch Copy Command (Strictly after Approval)**:
    *   `rsync -au --ignore-existing --files-from=[batch_file_list] [source] [target]` (Copy files in small, controlled batches using a manifest file list)
*   **Verification Commands**:
    *   `docker exec ems_prod_web ls -l` (Check file availability inside the running container)

---

## 10. Success Criteria

*   **Before Migration**:
    *   Detailed list of missing database-referenced assets is produced.
    *   Disk space availability on the host is confirmed to be sufficient for the required subset.
    *   Sample-based verification (random check of 50–100 files) successfully completed and documented.
    *   Mandatory dry run (`rsync --dry-run`) executed, and the exact count/manifest of files to transfer is verified.
    *   Explicit manual approval from the user is secured.
*   **After Migration**:
    *   The NEW SYSTEM `exhibition_system_prod` has all required media assets locally on disk.
    *   No broken images are shown on any active NEW SYSTEM page or generated PDF.
    *   All target uploads folders are writable by the container's webserver process.

---

## 11. Rollback Plan

*   **Rollback Triggers**: Disk space drops below 2GB, copy process hangs, or file permission errors break the NEW SYSTEM application.
*   **Granular Rollback Procedure**:
    1.  **Terminate Execution**: Immediately terminate the copy or synchronization process.
    2.  **Refer to Migration Manifest**: Read the migration change log (manifest) generated during the copy phase.
    3.  **Targeted Removal**: Delete **only** the specific files introduced during this migration, as listed in the manifest.
    4.  **No Wildcard Deletions**: Do not use generic directory deletion commands (like `rm -rf uploads/*` or `rm -rf *`) to avoid losing valid pre-existing target assets.
    5.  **User Sign-off**: Execute rollback commands only after explicit manual approval from the user.
    6.  **Operational Continuity**: Ensure the NEW SYSTEM remains fully operational and accessible during the rollback process.

---

## 12. Future GitHub Deployment & Uploads Protection Rules

To support future automation and Git-based deployments (CI/CD) for `exhibition_system_prod`, the following rules apply:
*   **Git Exclusion**:
    *   All `uploads/` directories and media subfolders must be explicitly excluded from Git versioning via `.gitignore`.
    *   The deployment pipeline (`git pull` or automated CI/CD runs) must update application codebase files only, never modifying, tracking, or overwriting filesystem assets.
*   **Asset Persistence**:
    *   Uploads must persist intact across all code deployments, container restarts, and image rebuilds.
    *   The deployment pipeline must never clear, delete, or overwrite the target `uploads` folders.
*   **Persistent Storage Architecture**:
    *   A robust persistent storage strategy (such as mapping `uploads` to a Docker host directory volume or utilizing external object storage) must be implemented and verified before enabling automated pipeline deployments.

---

## 13. Final Recommendation Section

*   **Conditions Required Before Retiring/Deleting `/home/kamran/exhibition_system` & `/var/www/html/exhibition_system`**:
    1.  The NEW SYSTEM `exhibition_system_prod` operates successfully for a minimum of 7 consecutive days with real traffic.
    2.  Nginx configurations have been updated to direct main domain traffic (`exhibit.com.pk`) to the new staging target.
    3.  A complete audit report proves that 100% of database-referenced files exist in the NEW SYSTEM and are fully readable.
    4.  A compressed backup archive of `/home/kamran/exhibition_system` (including source uploads and legacy database) is created and stored offsite.
    5.  Verification checklist (DNS resolution, DB query checks, and image loading audits) is signed off by the user.
