# Architecture

## Stack

| Layer | Original (2007) | Current |
|-------|-----------------|---------|
| Language | PHP 5 alpha | PHP 8.3 |
| Framework | Zend Framework 0.7–0.8 | ZF1 Future fork |
| Database | MySQL 5.0, MyISAM | MariaDB 11.1, InnoDB |
| Web server | Apache 1.3 | Apache 2.4 with mod_rewrite |
| Auth | Zend_Auth, MD5 hashing | Zend_Auth, MD5 hashing |
| Sessions | Server-side PHP | Server-side PHP (Zend_Session) |
| Deployment | Manual FTP | Docker |
| Dependencies | include_path | Composer, PSR-4 autoloading |

## Request Flow

```
Browser -> Apache -> mod_rewrite -> index.php -> ZF1 Router -> Controller -> Model -> View -> HTML
```

All interactions are synchronous form submissions. No API, no async, no messaging.

## Modules

```
application/
├── modules/
│   ├── default/    # Public: homepage, login, logout
│   ├── admin/      # Back-office: tests, questions, users, reports
│   └── tests/      # Student: browse tests, take test, view results
```

Each module has its own controllers, views, and forms. Models are shared.

## Data Layer

- Active Record via `Zend_Db_Table`, no ORM relationships — manual joins and lookups
- See [database.md](database.md) for schema, engine, and constraints

## Authentication

- `Zend_Auth` adapter checks login + MD5(password) against `users` table
- Identity stored in PHP session
- No password salting

## Authorization

Role stored in `users.usr_role` (`a`/`e`/`u`).

Enforcement:
- **Admin module**: controller checks session for identity + role
- **Tests module**: controller checks session for identity
- **Default module**: no checks

No middleware, no ACL, no per-action permissions.

## Session Management During Tests

When a student starts a test, all state goes into PHP session:

- Test ID, result record ID
- Start time, time limit
- Question list (possibly shuffled subset)
- Answer variants per question (possibly shuffled)
- Current question index
- Per-question start timestamp (one-per-page mode)

In one-per-page mode, answers are written to the database on each question submission. In all-on-page mode, all answers are submitted at once. Navigation state, question selection, and shuffle order exist only in the session.

**Consequence**: session expiry mid-test = lost progress, no recovery.

## Limitations

| Area | Issue |
|------|-------|
| Framework | ZF1 is EOL; community fork |
| Storage | No transactions or FK constraints used (see [database.md](database.md)) |
| Security | MD5 passwords, no salt, roles not enforced consistently |
| State | Session-only test progress, no recovery |
| Scale | Single server, vertical only |
| Interface | Server-rendered HTML, no REST API |
