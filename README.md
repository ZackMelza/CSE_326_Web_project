# CSE_326_Web_project

## Team

- Zacharias Melauin — AM 27185

## Ownership

- Zacharias Melauin: authentication flow, database setup, protected pages, search list, and project documentation

## Assignment Structure

```text
project-root/
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── register.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── includes/
│   ├── db.php
│   ├── functions.php
│   └── header.php
├── lessons_stuff/
│   ├── 2nd_aasigment
│   └── project_journal/
├── modules/
│   ├── dashboard.php
│   └── list.php
└── README.md
```

## Features

- Secure register, login, and logout flow
- Password hashing with `password_hash()` and verification with `password_verify()`
- PDO connection with prepared statements
- Protected dashboard page with session guard
- Protected `list.php` with keyword search through `GET`
- SQL schema plus demo seed data

## Database Design

### `users`

- `id`
- `username`
- `email`
- `password_hash`
- `role`
- `created_at`

### `posts`

- thematic table for project content
- linked to `users.id` through a foreign key
- searchable in `modules/list.php`

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
sudo apt install apache2 libapache2-mod-php php mariadb-server
sudo systemctl enable --now apache2
sudo systemctl enable --now mariadb
```

### 2. Enable MySQL support in PHP

Arch Linux:

```bash
sudo sed -i 's/^;extension=mysqli/extension=mysqli/' /etc/php/php.ini
sudo sed -i 's/^;extension=pdo_mysql/extension=pdo_mysql/' /etc/php/php.ini
sudo systemctl restart httpd
```

Debian/Ubuntu:

```bash
sudo apt install php-mysql
sudo systemctl restart apache2
```

### 3. Set Apache `DocumentRoot`

Set Apache to serve:

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

The app defaults to:

- `DB_HOST=localhost`
- `DB_PORT=3306`
- `DB_NAME=cse326_auth`
- `DB_USER=cse326_user`
- `DB_PASS=StrongPass123!`

You can override them through Apache or shell environment variables.

## Demo Accounts

All seeded demo users use:

- Password: `Password123!`

Emails:

- `admin@example.com`
- `writer@example.com`
- `viewer@example.com`

## Main Routes

- `/`
- `/auth/register.php`
- `/auth/login.php`
- `/auth/logout.php`
- `/modules/dashboard.php`
- `/modules/list.php`
