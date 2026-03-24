# M2-Safe Project Roadmap

This document explains how the project can grow toward the broader "Εφαρμογή Παρακολούθησης Πινάκων Διοριστέων" brief without breaking the latest `2nd_aasigment` requirements.

## Ground Rule

The latest grading target is the second assignment.

That means the following must remain valid at all times:

- `database/schema.sql`
- `database/seed.sql`
- `includes/db.php`
- `auth/register.php`
- `auth/login.php`
- `auth/logout.php`
- `modules/dashboard.php`
- `modules/list.php`
- the current PDO authentication flow
- the current protected dashboard
- the current searchable `list.php`

## What the Older Brief Adds

The earlier PDF describes a larger final system with four modules:

- Admin module
- Candidate module
- Search module
- API module

It also expects:

- a landing page that lets the user choose a module
- search, filter, and order capabilities
- role-based access by user type
- a richer domain model around candidates, specialties, lists, and tracking
- data import from the EEY source
- a more institutional, information-first interface

## Compatibility Strategy

We should extend the current M2 build instead of replacing it.

### Keep as-is

- `auth/` remains the main authentication area
- `modules/dashboard.php` remains the protected M2 dashboard
- `modules/list.php` remains the M2 searchable list deliverable
- `users` remains the base authentication table

### Expand carefully

- the current thematic `posts` table can later be replaced or supplemented by real domain tables
- additional modules should be added around the existing structure, not by deleting M2 pages
- the landing page can evolve from `index.php`
- role-based routing can be layered on top of the existing session fields

## Recommended Data Model Evolution

The current `posts` table is acceptable for M2, but the real project domain needs a richer schema. The safest next version would keep `users` and add domain tables such as:

- `specialties`
- `appointment_lists`
- `candidates`
- `candidate_list_entries`
- `tracked_candidates`
- `user_profiles`
- `notification_preferences`

### Why this is safer than immediate replacement

- M2 only requires one thematic table, and `posts` currently satisfies that
- dropping `posts` immediately would create unnecessary risk before the next grading milestone
- new tables can be introduced while keeping `modules/list.php` functional

## Module Mapping

The broader PDF can be mapped onto the current repository like this:

### Current M2 pages

- `index.php` -> can become the landing page
- `auth/*` -> already correct foundation for login/register/logout
- `modules/dashboard.php` -> can remain the protected candidate/admin landing page
- `modules/list.php` -> can evolve into the public or protected search/list view

### Future module expansion

- `modules/admin/` -> admin dashboard, manage users, manage lists, reports
- `modules/candidate/` -> profile, track my applications, track others
- `modules/search/` -> public search dashboard, filters, statistics
- `api/` -> JSON endpoints for third-party access

## UI Direction

The current interface is cleaner than before, but the EEY reference suggests a more formal institutional style.

The next UI phase should move toward:

- Greek-first labels where appropriate
- more tables and structured filters
- breadcrumb-style navigation
- archive and reporting layouts
- less "hero section" emphasis
- more information-dense presentation

This should be done progressively so the current M2 pages remain stable.

## Proposed Implementation Order

### Phase A: Architecture

- define the target data model for specialties, candidates, and lists
- decide how roles map to modules
- document route ownership and permissions

### Phase B: Landing and Navigation

- turn `index.php` into a true module selector
- add consistent menus inside each module
- keep the existing M2 routes reachable

### Phase C: Search Module

- upgrade search from generic post search to candidate/specialty search
- add filters and ordering
- keep the `GET`-based bookmarkable behavior required by M2

### Phase D: Candidate Module

- add user profile management
- add candidate linking flow
- add tracked candidates

### Phase E: Admin Module

- add user management
- add list management
- add reporting/statistics dashboards

### Phase F: API

- expose read-only API endpoints for selected data
- secure access appropriately

## What We Should Avoid

- removing or renaming the current M2-required files too early
- replacing the working auth flow during grading period
- tying the whole project to unverified scraped data before the domain model is ready
- redesigning the UI so aggressively that it stops looking like coursework deliverables

## Immediate Next Step

The best next move is to design the real database schema for the broader project while preserving the current M2 schema and routes.
