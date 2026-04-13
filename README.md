# Testy

Test management system for educational institutions. Teachers create tests, students take them, results are scored automatically.

## Quick Start

```bash
docker-compose up -d
# http://localhost:8080
```

| Account | Login | Password | Entry point |
|---------|-------|----------|-------------|
| Admin | `admin` | `admin` | `/admin` |
| Student | `student` | `test` | `/` |

## Documentation

- [Overview](docs/README.md) — roles, question types, running the app
- [Use Cases](docs/use-cases.md) — what users can do, by role
- [Architecture](docs/architecture.md) — stack, modules, data flow
- [Database](docs/database.md) — schema, ER diagram, scoring

## History

College graduation project, 2007. PHP 5 alpha + early Zend Framework. Modernized to PHP 8.3 / MariaDB 11.1 / Docker — original architecture preserved.
