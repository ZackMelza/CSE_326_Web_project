# Assignment Alignment Audit

This file maps the teacher instructions from `lessons_stuff/2nd_aasigment` to the current implementation.

## Repository Structure

### Required

- `database/schema.sql`
- `database/seed.sql`
- `includes/db.php`
- `auth/register.php`
- `auth/login.php`
- `auth/logout.php`
- `modules/dashboard.php`
- `modules/list.php`
- `README.md`

### Current status

- implemented

## Database Schema

### Required

- `users` table with `id`, `username`, `email`, `password_hash`, `role`, `created_at`
- at least one thematic table
- foreign key from thematic table to `users`

### Current implementation

- `database/schema.sql` defines `users`
- `database/schema.sql` defines `posts`
- `posts.user_id` is a foreign key to `users.id`

### Current status

- implemented

## Seed Data

### Required

- at least 3 demo users
- at least 5 thematic records

### Current implementation

- `database/seed.sql` inserts 3 demo users
- `database/seed.sql` inserts 5 demo posts

### Current status

- implemented

## PDO Connection

### Required

- PDO
- `ERRMODE_EXCEPTION`
- `FETCH_ASSOC`
- safe `try/catch`
- no exposed DB error message through `die($e->getMessage())`

### Current implementation

- `includes/db.php` uses PDO
- error mode is `PDO::ERRMODE_EXCEPTION`
- fetch mode is `PDO::FETCH_ASSOC`
- database failures are converted to a generic runtime exception message

### Current status

- implemented

## Register Page

### Required

- POST data reading
- `trim()` on text fields
- no `trim()` on password
- server-side validation for empty fields
- email validation with `filter_var()`
- password length at least 8
- confirm password match
- uniqueness check with prepared statement
- password hashing
- insert with prepared statement
- redirect to `login.php?registered=1`

### Current implementation

- handled in `auth/register.php`
- all text fields are trimmed
- password fields are not trimmed
- all validation errors are collected before display
- email and username uniqueness are checked with prepared statements
- password uses `password_hash()`
- successful registration redirects to `auth/login.php?registered=1`

### Current status

- implemented

## Login Page

### Required

- `session_start()` first
- `SELECT * FROM users WHERE email = :email`
- `password_verify()`
- set `user_id`, `role`, `username` in session
- generic failure message

### Current implementation

- handled in `auth/login.php`
- `session_start()` is the first executable line
- login query uses `SELECT * FROM users WHERE email = :email`
- password checking uses `password_verify()`
- session stores `user_id`, `role`, and `username`
- failure message is generic

### Current status

- implemented

## Logout Page

### Required

- `session_start()`
- `session_destroy()`
- redirect
- `exit`

### Current implementation

- handled in `auth/logout.php`

### Current status

- implemented

## Protected Dashboard

### Required

- session guard
- show username and role
- use `htmlspecialchars()`
- logout link

### Current implementation

- handled in `modules/dashboard.php`
- session variables are escaped with helper `e()`
- logout link points to `auth/logout.php`

### Current status

- implemented

## List Page With Keyword Search

### Required

- session guard
- select from thematic table with prepared statement
- keyword search through `GET`
- `WHERE LIKE :kw`
- `GET` search form
- `htmlspecialchars()` on all output
- result presentation in table or cards

### Current implementation

- handled in `modules/list.php`
- query uses prepared PDO statements
- search uses `GET` parameter `keyword`
- filtering uses `LIKE :keyword`
- values are escaped through `e()`
- results are displayed as cards

### Current status

- implemented

## Security Rules

### Required

- prepared statements only
- hashed passwords only
- escaped output
- `exit` after redirects
- no exposed exception details

### Current implementation

- applied across the new PDO-based auth and modules pages

### Current status

- implemented

## README Submission Requirements

### Required

- student names and AM
- who handled which file or function
- installation instructions

### Current implementation

- covered in `README.md`

### Current status

- implemented

## Notes

- the original root pages `login.php`, `register.php`, and `logout.php` were kept as compatibility redirects
- the authoritative assignment implementation lives in `auth/`, `modules/`, `database/`, and `includes/db.php`
- if more team members are added later, `README.md` should be updated with their names, AM numbers, and ownership areas
