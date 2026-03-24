# Chronological Project History

## Phase 1: Initial Repository State

At the beginning of the backend work, the repository already contained a basic PHP website structure with root pages such as `index.php`, `login.php`, `register.php`, and `logout.php`, plus several placeholder folders like `admin/`, `submit/`, and `modules/`. Most of the authentication-related files were empty, and the include files for database configuration and helper functions were either missing or blank. In practice, the repository had the shell of a PHP project but not the backend implementation requested by the assignment.

### Observed state

- `login.php`, `register.php`, and `logout.php` were empty
- `includes/config.php` and `includes/functions.php` were empty
- there was no required `auth/` directory
- there was no `database/` directory with `schema.sql` and `seed.sql`
- there was no protected `modules/dashboard.php`
- there was no `modules/list.php` with keyword search

## Phase 2: First Authentication Implementation

The first backend milestone was to make the site functional as quickly as possible. A simple register/login/logout flow was added using PHP sessions and a MySQL database. The initial implementation used `mysqli` and the root-level pages directly. Passwords were stored securely with `password_hash()` and checked with `password_verify()`. A small home page was also created to show whether the user was logged in or not.

### What was added in this phase

- database connection logic
- helper functions for redirects and session handling
- registration form and insert flow
- login form and password verification
- logout flow with session destruction
- a starter SQL file for the users table

### Why this phase mattered

This gave the project a working backend foundation quickly. Even though it did not yet match the teacher's exact required file structure, it proved that sessions, forms, hashing, and database interaction were all working end-to-end.

## Phase 3: Environment Debugging

When the first version was tested in Apache, the site returned HTTP 500 errors. The root cause was not PHP syntax. The problem was that the `mysqli` extension was not enabled in the local PHP installation. After that was resolved, the next issue was database connectivity: MariaDB was either not yet configured or not reachable with the selected credentials.

### Problems solved

- PHP crashed because `mysqli_report()` was unavailable
- PHP MySQL modules had to be enabled in `/etc/php/php.ini`
- MariaDB had to be initialized and started
- a local database user had to be created for the project

### Result

Once PHP and MariaDB were configured correctly, the original auth flow started working in the browser.

## Phase 4: Git Version Control Milestone

After the basic auth flow was working, the first meaningful backend commit was created and pushed to GitHub. This was important both for project history and because the assignment explicitly requires clear individual commits.

### Commit significance

- preserved the first complete auth milestone
- established visible backend progress in GitHub
- satisfied part of the teacher's grading expectations around commit history

## Phase 5: Assignment Compliance Refactor

Later, the teacher instructions in `lessons_stuff/2nd_aasigment` were reviewed carefully. That review showed that the project still did not satisfy several mandatory requirements. The biggest mismatch was architectural: the teacher requires a strict directory layout and explicitly asks for a PDO-based database layer instead of `mysqli`.

### Missing requirements identified

- `database/schema.sql`
- `database/seed.sql`
- `includes/db.php`
- `auth/register.php`
- `auth/login.php`
- `auth/logout.php`
- `modules/dashboard.php`
- `modules/list.php`
- a foreign-key-linked thematic table
- PDO with prepared statements

### Refactor decision

Instead of patching the older layout, the codebase was reorganized around the required assignment structure while preserving compatibility with the existing root URLs.

## Phase 6: PDO Migration and Required Structure

The database layer was rewritten around PDO in `includes/db.php`. The connection now uses `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` and `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`, exactly as the assignment asks. The old root pages were turned into lightweight redirect entry points, while the real implementation moved into the required `auth/` and `modules/` directories.

### Files added in this phase

- `includes/db.php`
- `auth/register.php`
- `auth/login.php`
- `auth/logout.php`
- `modules/dashboard.php`
- `modules/list.php`
- `database/schema.sql`
- `database/seed.sql`

### Key backend changes

- register now validates all fields server-side before showing errors
- login now uses `SELECT * FROM users WHERE email = :email`
- sessions store `user_id`, `role`, and `username`
- logout destroys the session and redirects safely
- every redirect is followed by `exit`
- output from users and sessions is wrapped in `htmlspecialchars()` through helper `e()`

## Phase 7: Thematic Table and Search Deliverable

The assignment requires one thematic table besides `users`, connected with a foreign key, plus a working protected `list.php` page with keyword search. A `posts` table was chosen because it provides a neutral and easy-to-present content model. Each post belongs to a user through `posts.user_id -> users.id`.

### Why `posts` was chosen

- simple enough to implement quickly and correctly
- easy to demonstrate in class
- naturally supports search by title, category, and summary

### Search implementation

- search uses `GET`, so results are bookmarkable
- filtering uses `LIKE :keyword`
- results are displayed as cards
- the page is protected by a session guard

## Phase 8: Documentation for Final Presentation

Because the project will need a final presentation at the end of the term, a dedicated documentation area was created in `lessons_stuff/project_journal/`. This journal is intended to grow over time and capture both the implementation details and the reasoning behind each major change.

### Documentation strategy

- keep a readable chronological history
- keep a separate assignment alignment document
- update both as the project evolves

## Phase 9: Database Import Hardening

During testing, importing `database/seed.sql` into an older local database failed because the original `users` table from the earlier `mysqli` version did not contain the newer `role` column. The original `schema.sql` used `CREATE TABLE IF NOT EXISTS`, which is safe for clean installs but does not upgrade an already existing table.

### Fix applied

- `database/schema.sql` now upgrades the existing `users` table with `ALTER TABLE ... ADD COLUMN IF NOT EXISTS`
- `database/seed.sql` now refreshes `password_hash` and `role` on duplicate keys instead of updating only the email

### Why this matters

This makes the SQL setup more robust for future development. A local database created before the PDO refactor can now be brought up to date without forcing a full database drop every time.

## Phase 10: Teacher-Safe UI Polish

After the backend requirements were covered, the interface was polished to make the project easier to present and more professional to review. The goal was not to redesign the flow or introduce unnecessary frontend complexity. The goal was to improve clarity, consistency, and visual quality while preserving the assignment-compliant backend behavior.

### Visual changes applied

- a shared visual system was added through `assests/css/styles.css`
- `includes/header.php` now loads the shared stylesheet and a stronger font
- `auth/register.php` and `auth/login.php` were redesigned with a split layout that explains the security flow
- `modules/dashboard.php` was upgraded into a clearer protected landing page
- `modules/list.php` was restyled into a cleaner search-and-results experience
- `index.php` was refreshed so the home page better communicates the project deliverables

### Why this matters

The project now reads more clearly in a demo setting. A teacher can move through the homepage, auth pages, dashboard, and list page and immediately understand what each part of the backend delivers. The code still keeps the same routing, validation, session handling, and security logic as before.

## Phase 11: Roadmap Reconciliation

After the larger project brief for the EEY-style application was reviewed, it became clear that the repository now sits at an important boundary: it satisfies the latest M2 backend assignment, but it does not yet implement the full long-form project vision with Admin, Candidate, Search, and API modules.

### Decision taken

- the second assignment remains the hard constraint
- the older PDF is treated as long-term product direction
- future work must extend the current M2 structure instead of replacing it

### Output of this phase

- a roadmap document was added to explain how to evolve the project safely
- the roadmap keeps `auth/`, `modules/dashboard.php`, and `modules/list.php` intact
- the next recommended milestone is a domain-specific database redesign that still preserves M2 compliance

## Current State Summary

At this point, the project includes a full backend authentication flow, a PDO database layer, a required SQL schema and seed, a protected dashboard, and a protected searchable list page. It now matches the core structure and behavior described by the teacher for the second assignment, with compatibility redirects left in place for the original root URLs.
