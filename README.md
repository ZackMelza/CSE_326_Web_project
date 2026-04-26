# CSE_326 Web Project

## Overview

This repository is now a small PHP and MariaDB appointment-tracking application built for the CSE 326 coursework. It extends the earlier M2 authentication submission into a broader project that matches the screenshot checklist: a relational MySQL schema, role-based sessions, CRUD for four entities, a responsive UI, and JSON API endpoints with a Postman collection.

## Team

- Zacharias Melauin — AM 27185

## Ownership

- Zacharias Melauin: schema design, authentication flow, session guards, CRUD modules, search module, API endpoints, Postman collection, UI polish, README, and project journal

## Checklist Coverage

- MySQL relational database with 7 tables and foreign-key relations
- Full CRUD for 4 entities: `specialties`, `appointment_lists`, `candidates`, `candidate_list_entries`
- 6 JSON API endpoints under `api/`
- Login and session handling with 2 roles: `admin` and `member`
- Responsive UI for landing, dashboard, search, tracking, and admin screens
- README plus updated project journal in `lessons_stuff/project_journal/`

## Database Design

The main schema is defined in [database/schema.sql](/srv/http/webeng/CSE_326_Web_project/database/schema.sql:1).

### Tables

- `users`: authentication accounts with `role`
- `specialties`: specialty codes and sectors
- `appointment_lists`: yearly lists linked to a specialty
- `candidates`: candidate profile records
- `candidate_list_entries`: ranked entries linking candidates to lists
- `tracked_candidates`: user-specific watchlist records
- `audit_logs`: simple audit trail for admin CRUD actions

## Main Screens

- `/index.php` landing page and module selector
- `/auth/register.php` register
- `/auth/login.php` login
- `/modules/dashboard.php` protected user dashboard
- `/modules/list.php` protected candidate search and filter page
- `/modules/tracking/dashboard.php` member tracking CRUD
- `/modules/admin/dashboard.php` admin hub
- `/modules/specialties/dashboard.php` specialty CRUD
- `/modules/lists/dashboard.php` appointment list CRUD
- `/modules/candidates/dashboard.php` candidate CRUD
- `/modules/entries/dashboard.php` candidate entry CRUD

## API Endpoints

- `/api/stats/index.php`
- `/api/specialties/index.php`
- `/api/lists/index.php`
- `/api/candidates/index.php`
- `/api/entries/index.php`
- `/api/search/index.php`

The Postman collection is stored in [postman/CSE_326_Web_project.postman_collection.json](/srv/http/webeng/CSE_326_Web_project/postman/CSE_326_Web_project.postman_collection.json:1).

## Installation

### 1. Install Apache, PHP, and MariaDB

Arch Linux:

```bash
sudo pacman -S apache php php-apache mariadb
sudo systemctl enable --now httpd
sudo systemctl enable --now mariadb
```

Debian/Ubuntu:

```bash
sudo apt update
sudo apt install apache2 libapache2-mod-php php php-mysql mariadb-server
sudo systemctl enable --now apache2
sudo systemctl enable --now mariadb
```

### 2. Ensure PHP MySQL support is enabled

Arch Linux:

```bash
sudo sed -i 's/^;extension=mysqli/extension=mysqli/' /etc/php/php.ini
sudo sed -i 's/^;extension=pdo_mysql/extension=pdo_mysql/' /etc/php/php.ini
sudo systemctl restart httpd
```

Debian/Ubuntu:

```bash
sudo systemctl restart apache2
```

### 3. Set Apache `DocumentRoot`

Serve this directory:

`/srv/http/webeng/CSE_326_Web_project`

### 4. Create Database and Import SQL

```bash
sudo mariadb < /srv/http/webeng/CSE_326_Web_project/database/schema.sql
sudo mariadb <<'SQL'
CREATE USER IF NOT EXISTS 'cse326_user'@'localhost' IDENTIFIED BY 'StrongPass123!';
GRANT ALL PRIVILEGES ON cse326_auth.* TO 'cse326_user'@'localhost';
FLUSH PRIVILEGES;
SQL
sudo mariadb cse326_auth < /srv/http/webeng/CSE_326_Web_project/database/seed.sql
```

### 5. Optional Environment Variables

Defaults are defined in `includes/db.php`:

- `DB_HOST=localhost`
- `DB_PORT=3306`
- `DB_NAME=cse326_auth`
- `DB_USER=cse326_user`
- `DB_PASS=StrongPass123!`

## Demo Accounts

All seeded users use password `Password123!`.

- `admin@example.com` -> role `admin`
- `writer@example.com` -> role `member`
- `viewer@example.com` -> role `member`

## Notes

- The root `login.php`, `register.php`, and `logout.php` files remain as compatibility redirects.
- The old M2 coursework routes still exist, but now point into a richer domain model.
- The project journal documents the transition from the smaller auth submission to the current checklist-complete version.
