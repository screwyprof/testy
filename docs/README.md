# Testy

Test management system for educational institutions. Teachers create and configure tests, students take them online, results are scored automatically.

Built as a 2007 college graduation project to fill a gap: the institution's official testing platform was purely for formal exams — monitored, rigid, no feedback. Students never saw correct answers; teachers had no per-question statistics. Testy was the unofficial alternative: unlimited attempts, correct answer reveal after completion so students could learn from mistakes, and per-question statistics so teachers could spot weak topics. It ran alongside the official system for day-to-day practice and informal assessment.

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
