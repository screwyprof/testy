# Testy

Test management system for educational institutions. Teachers create and configure tests, students take them online, results are scored automatically.

## Running

```bash
docker-compose up -d
# http://localhost:8080
```

| Account | Login | Password | Entry point |
|---------|-------|----------|-------------|
| Admin | `admin` | `admin` | `/admin` |
| Educator | `editor` | `editor` | `/admin` |
| Student | `student` | `test` | `/` |

## Documentation

- [Use Cases](use-cases.md) — roles, question types, flows with screenshots
- [Architecture](architecture.md) — stack, modules, auth, session handling
- [Database](database.md) — schema, scoring, grade scale
