# Presentation Guide

Use this as the speaking script for the project presentation. The goal is to show that the repository is not just a set of pages, but a complete PHP and MariaDB application that satisfies the lesson requirements.

## 1. One-Minute Project Summary

This project is a PHP and MariaDB appointment-tracking system for candidates and appointment lists. It started from the backend assignment requirements: authentication, sessions, PDO, protected pages, and search. It was then expanded to match the broader checklist: a relational database with 7 tables, full CRUD for 4 main entities, 2 user roles, responsive UI, and JSON API endpoints that can be tested in Postman.

The main idea is:

- admins manage the system data
- members can search appointment entries and track candidates
- the API exposes selected data as JSON
- all database access uses PDO and prepared statements

## 2. Requirements Mapping

### MySQL database with 6-7 tables and relationships

File: `database/schema.sql`

The project has 7 tables:

- `users`: stores login accounts and the user role
- `specialties`: stores specialty codes, names, and sectors
- `appointment_lists`: yearly lists connected to specialties
- `candidates`: stores candidate profiles
- `candidate_list_entries`: joins candidates to appointment lists with ranking and status
- `tracked_candidates`: stores each user's personal candidate watchlist
- `audit_logs`: records admin create, update, and delete actions

Important relationships:

- `appointment_lists.specialty_id` -> `specialties.id`
- `candidate_list_entries.candidate_id` -> `candidates.id`
- `candidate_list_entries.list_id` -> `appointment_lists.id`
- `tracked_candidates.user_id` -> `users.id`
- `tracked_candidates.candidate_id` -> `candidates.id`
- `audit_logs.user_id` -> `users.id`

What to say:

"I used foreign keys so the database protects the relationships. For example, a candidate entry cannot exist without a real candidate and a real appointment list."

### CRUD for 4 basic entities

The admin role can create, read, update, and delete:

- specialties: `modules/specialties/dashboard.php`
- appointment lists: `modules/lists/dashboard.php`
- candidates: `modules/candidates/dashboard.php`
- candidate list entries: `modules/entries/dashboard.php`

Each CRUD page follows the same pattern:

- `session_start()`
- include `db.php` and `functions.php`
- call `require_admin()`
- read form data from `POST`
- validate required fields
- use prepared SQL statements for insert, update, or delete
- redirect after successful POST
- show current records in a table

What to say:

"The CRUD pages are admin-only. Normal members can search and track candidates, but they cannot manage core database records."

### API endpoints and Postman

Folder: `api/`

Endpoints:

- `api/stats/index.php`
- `api/specialties/index.php`
- `api/lists/index.php`
- `api/candidates/index.php`
- `api/entries/index.php`
- `api/search/index.php`

Postman file:

- `postman/CSE_326_Web_project.postman_collection.json`

The API bootstrap file is `api/_bootstrap.php`. It sets the JSON response header, opens the database connection, and provides `api_response()`.

What to say:

"The API endpoints are read-only JSON endpoints for demo and integration. The Postman collection contains ready requests for the teacher to test quickly."

### Login and sessions with 2 roles

Files:

- `auth/register.php`
- `auth/login.php`
- `auth/logout.php`
- `includes/functions.php`

Roles:

- `admin`
- `member`

Login flow:

1. User enters email and password in `auth/login.php`.
2. The code queries `users` by email using a prepared statement.
3. It checks the password with `password_verify()`.
4. If login succeeds, it stores `user_id`, `role`, and `username` in `$_SESSION`.
5. Protected pages call `require_auth()` or `require_admin()`.

What to say:

"The authorization is server-side. Admin links may appear in the UI, but the real protection is the `require_admin()` check at the top of admin pages."

### Responsive UI

Files:

- `includes/header.php`
- `assests/css/styles.css`

The app uses Bootstrap plus custom CSS. The important layouts are:

- top navigation bar
- dashboard grids
- responsive CRUD layouts
- card-based search results
- mobile breakpoints in CSS

What to say:

"The same pages work on desktop and mobile because the layouts collapse with CSS media queries and Bootstrap grid classes."

## 3. Important Files To Explain

### `includes/db.php`

This file creates the PDO database connection.

Key points:

- reads database settings from environment variables or defaults
- uses MySQL charset `utf8mb4`
- enables `PDO::ERRMODE_EXCEPTION`
- uses `PDO::FETCH_ASSOC`
- returns a single reused PDO connection through a static variable
- hides real database errors from users

Presentation line:

"All database access goes through this one helper, so connection behavior is consistent across pages and APIs."

### `includes/functions.php`

This file contains shared helper functions.

Important helpers:

- `redirect()`: sends a Location header and exits
- `is_logged_in()`: checks session state
- `require_auth()`: blocks anonymous users
- `require_admin()`: blocks non-admin users
- `e()`: escapes output with `htmlspecialchars()`
- `post_string()` and `post_int()`: normalize form input
- `log_action()`: writes audit records

Presentation line:

"The helpers keep repeated security behavior in one place, especially session guards and escaped output."

### `auth/register.php`

Registration does:

- server-side validation
- email format validation with `filter_var()`
- password length check
- confirm password match
- duplicate email and username checks
- password hashing with `password_hash()`
- insert using prepared statements
- redirect to login on success

Presentation line:

"Passwords are never stored as plain text. The database stores the hash generated by PHP."

### `auth/login.php`

Login does:

- starts the session
- checks email and password
- verifies password with `password_verify()`
- stores session fields
- redirects to the dashboard

Presentation line:

"After login, the session becomes the source of truth for the current user id, role, and username."

### `modules/list.php`

This is the protected search page.

It supports:

- keyword search
- status filter
- specialty filter
- GET-based query parameters
- multi-table joins
- prepared statements for dynamic filters
- escaped output in result cards

Presentation line:

"The search page joins candidates, list entries, appointment lists, and specialties, so it demonstrates the relational schema in a user-facing feature."

### CRUD pages

Example: `modules/specialties/dashboard.php`

CRUD pages show the same structure:

- check admin role
- handle delete if `action=delete`
- read form data
- validate input
- insert or update depending on whether an id exists
- log action
- redirect after success
- list records in a table

Presentation line:

"I reused the same pattern for all four CRUD entities, so the code is predictable and easier to maintain."

### `modules/tracking/dashboard.php`

This is the member-level feature.

It lets a logged-in user:

- pick a candidate
- add a tracking label
- update an existing tracked candidate through duplicate-key update
- remove only their own tracking records

Important detail:

The delete query includes both `id` and `user_id`, so one user cannot delete another user's tracking record.

Presentation line:

"This page shows role-specific behavior for normal members, not only admins."

### `api/search/index.php`

This endpoint mirrors the HTML search feature.

It accepts:

- `q`
- `status`
- `specialty_id`

It returns:

- selected filters
- matching records as JSON

Presentation line:

"The API search endpoint uses the same relational joins as the UI search, but returns JSON for Postman or external clients."

## 4. Suggested Live Demo Flow

Use this order in the presentation:

1. Open `README.md` and briefly show the checklist coverage.
2. Open `database/schema.sql` and explain the 7 tables and relationships.
3. Open the app home page and show the module selector.
4. Login as admin:

```text
admin@example.com
Password123!
```

5. Show the protected dashboard.
6. Open Admin Hub.
7. Show one CRUD page, preferably Specialties or Candidates.
8. Create or edit a small record.
9. Show the search page and filter by keyword/status/specialty.
10. Open Tracking and show member-style watchlist behavior.
11. Open one API endpoint in the browser, for example:

```text
/api/stats/index.php
/api/search/index.php?q=maria&status=active
```

12. Show the Postman collection file.
13. Mention that the project journal documents the development history.

## 5. Common Questions And Answers

### Why PDO?

PDO supports prepared statements and exception handling. It helps prevent SQL injection and keeps database access consistent.

### How do you prevent SQL injection?

User input is passed through prepared statements with named parameters such as `:email`, `:keyword`, and `:id`. The SQL and the user data are separated.

### How are passwords protected?

Registration uses `password_hash()`. Login uses `password_verify()`. The plain password is never saved in the database.

### How are pages protected?

Normal protected pages call `require_auth()`. Admin pages call `require_admin()`. These functions check the PHP session before allowing access.

### How do roles work?

The `users` table has a `role` column. When the user logs in, the role is stored in `$_SESSION['role']`. Admin pages require that role to be `admin`.

### Where is CRUD implemented?

CRUD is implemented in the admin module pages for specialties, appointment lists, candidates, and candidate list entries.

### What makes the database relational?

Foreign keys connect the tables. For example, entries connect a candidate to an appointment list, and appointment lists connect to specialties.

### What does the API do?

The API exposes selected database data as JSON. It is useful for Postman testing and demonstrates backend endpoints separate from HTML pages.

### What is the audit log?

When admins create, update, or delete important records, `log_action()` records the action in `audit_logs`.

## 6. Weak Points To Be Honest About

These are acceptable to mention if asked:

- The API is read-only; CRUD is handled through protected HTML forms.
- There is no CSRF token yet, so that would be a future security improvement.
- The app uses simple string roles instead of a full permissions table.
- The project is coursework-scale, so the UI is practical but not a production-grade admin system.

## 7. Final Closing Statement

"The project satisfies the lesson requirements by combining a relational MySQL schema, secure PHP authentication, role-based protected modules, CRUD screens, responsive UI, and JSON API endpoints. The code is organized around shared helpers, PDO prepared statements, and clear module folders, so each requirement can be demonstrated directly from the repository."
