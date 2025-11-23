# Testy

Educational assessment application for teachers to create and administer tests, and for students to take them.

## What It Does

- **Test Creation**: Teachers create tests with 5 question types (single choice, ordering, multiple choice, selection, ranking)
- **Test Configuration**: Set time limits, randomization options, question display modes
- **Test Taking**: Students take timed tests with automatic scoring
- **Results & Analytics**: Individual results and performance statistics

## Technical Details

- **Framework**: Zend Framework 1 (modernized to PHP 8.3)
- **Database**: MySQL 3 (modernised to MariaDB 11.1) with 6 core tables
- **Architecture**: MVC monolith with 3 modules (Default, Admin, Tests)
- **Authentication**: Basic user roles with Apache admin protection

## Quick Start

```bash
# Using Docker
docker-compose up -d

# Access the application
http://localhost:8080
```

## Test Accounts

**Admin Access:**
- Username: `admin`
- Password: `admin`
- URL: http://localhost:8080/admin

**Student Access:**
- Username: `student`
- Password: `test`

## Documentation

See [docs/original-state.md](docs/original-state.md) for detailed technical analysis of the current system.

## History

Created in 2007 as a college graduation project using PHP 5 alpha and early Zend Framework. Originally written in Russian for Russian-speaking educational institutions. The application has been slightly modernized to run on current PHP versions, the core functionality preserved as it was back then.
