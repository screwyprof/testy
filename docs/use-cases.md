# Use Cases

## Authentication

1. Submit login + password
2. DB lookup, MD5 comparison
3. Success: session created, redirect by role
4. Failure: login form with error

![Login](images/screenshots/02-login.png)

No authorization middleware. Module-level checks only: tests module requires identity, admin module requires identity + role.

---

## Admin

### Manage Users

1. Browse paginated user list
2. Create user: login, password, name, role
3. Edit user
4. Delete user (hard delete, with confirmation)

![User management](images/screenshots/07-admin-users.png)

Admins have all teacher capabilities plus user management.

---

## Teacher

### Create a Test

1. Open admin panel
2. Create test:
   - Title, description
   - Time limit (minutes in UI, stored as seconds in DB; 0 = unlimited)
   - Availability window (start/stop timestamps)
   - Questions to show (0 = all, N = random selection of N)
   - Shuffle questions, shuffle answers (flags)
   - Show correct answers after completion (flag)
   - Display mode: one per page / all per page
3. Add questions — select type, enter text, enter answer variants with correct marking
4. Enable test
5. Test becomes visible to students when within availability window

![Test list](images/screenshots/04-admin-tests.png)

![Test configuration](images/screenshots/05-admin-test-properties.png)

![Question list](images/screenshots/06-admin-questions.png)

### Question Types

| Type | Name | Input | Scoring |
|------|------|-------|---------|
| 1 | Free text | Textarea | All or nothing (exact match, case-sensitive, trimmed) |
| 2 | Ordering | Position dropdowns | All or nothing |
| 3 | Single choice | Radio buttons | All or nothing |
| 4 | Multiple selection | Checkboxes | Partial credit: 100%/N per correct pick; any wrong pick = 0% |
| 5 | Ranking | Priority dropdowns | All or nothing |

### Analyse Question Effectiveness

Per-question statistics across all submissions:

- Number of respondents
- Average score
- Per-variant breakdown: times selected, percentage
- Correctness summary: correct / incorrect / partial (counts + %)

![Question statistics](images/screenshots/18-admin-question-stats.png)

Computed on the fly from raw data. No pre-aggregated tables. Per-question only — no test-level aggregate statistics.

### View Reports

1. Browse results across all tests
2. Filter by test — per student: name, score, grade, time, time-exceeded flag
3. Drill into a result — per question: text, student answer, correct answer, score
4. Delete a result (hard delete, with confirmation)

![Reports](images/screenshots/17-admin-reports-with-data.png)

---

## Student

### Take a Test

1. Browse active tests (enabled, within availability window)
2. Start test — system creates a result record, selects and optionally shuffles questions
3. Answer questions:
   - **One-per-page**: submit answer, see next
   - **All-on-page**: answer everything, submit once
4. View results — score (%) and grade (1–5)
5. Review answers (if enabled) — per question: your answer vs correct answer

![Test list](images/screenshots/09-student-test-list.png)

#### One-per-page mode

Each question shown individually.

![Free text input (type 1)](images/screenshots/10-student-question.png)

![Ordering (type 2)](images/screenshots/13-student-question-4.png)

![Multiple selection (type 4)](images/screenshots/12-student-question-3.png)

![Ranking (type 5)](images/screenshots/11-student-question-2.png)

![Results — one-per-page mode](images/screenshots/15-student-results.png)

![Answer detail](images/screenshots/16-student-answer-detail.png)

#### All-on-page mode

All questions on a single page with a countdown timer. Submitted at once.

![All questions on one page](images/screenshots/19-student-all-questions.png)

![Results — all-on-page mode](images/screenshots/20-student-results-all.png)

![Answer detail with correct answers shown](images/screenshots/21-student-answer-correct.png)

**Timer**: if a time limit is set, the system checks elapsed time on each submission. Exceeding the limit auto-finishes the test. In all-on-page mode, a countdown timer is visible at the top. In one-per-page mode, enforcement is server-side only — no visible timer.

**Per-question timing**: tracked only in one-per-page mode (results show "Time Taken" column). All-on-page mode has no per-question timing.

**Session loss**: test state lives in PHP session. Session expiry = lost progress, no recovery. In practice never an issue — tests are short enough to complete within session lifetime.
