# Testy - Current Architecture Documentation

## Quick Overview

Educational assessment application built as a graduation project (PHP 5 alpha + ZF pre-1.0) and modernized to PHP 8.3. to make it run on contemporary systems. Demonstrates clean MVC architecture with comprehensive testing functionality - perfect legacy system for modernization demonstrations.

---

## Technology Stack

- **Framework:** Zend Framework 1 (ZF1 Future fork)
- **PHP:** 5.x (original) → 8.3 (modernized)
- **Database:** MySQL 3.x (original) → MariaDB 11.1 (MyISAM)
- **Server:** Apache 1.3.x (original) → Apache 2.4
- **Deployment:** Originally manual FTP → Now Docker

---

## Architecture Overview

### Pattern
**MVC Monolith** with 3 modules in ZF1 structure:

```ascii
Apache → index.php → ZF1 Router → Controller → Model → View → Response

┌─────────────────┐
│   Default       │  ← Public interface (login, homepage)
├─────────────────┤
│   Admin         │  ← Test management, user admin (Apache basic auth)
├─────────────────┤
│   Tests         │  ← Test-taking interface
└─────────────────┘
```

### Data Layer
- **Active Record** via Zend_Db_Table
- **MyISAM Database** (no transactions, table-level locking)
- **Manual Relationships** (no foreign key constraints)

---

## Database Schema

### Core Tables (6)
```ascii
users           ← usr_id, usr_login, usr_passwd(MD5), usr_role(a/e/u)
└── results     ← rst_usr_id, rst_test_id, scoring data
    └── results_answers ← detailed answer tracking

tests           ← test_id, timing, config (randomization, display)
└── questions   ← qst_test_id, qst_type (1-5), qst_text
    └── answers   ← ans_qst_id, ans_text, ans_is_correct
```

### Question Types
1. Single choice (radio)
2. Ordering (drag-drop)
3. Multiple choice (checkbox, single correct)
4. Multiple selection (checkbox, multiple correct)
5. Ranking (weighted ordering)

## Key Features

### Test System
- **5 Question Types**: Single/multiple choice, ordering, ranking
- **Test Config**: Timing, randomization, display modes
- **Scoring**: Complex algorithms with partial credit

### Assessment Flow
1. **Teacher Setup**: Create tests + questions via admin interface
2. **Student Access**: Login and take tests with timer
3. **Immediate Results**: Auto-scoring with detailed feedback
4. **Analytics**: Performance reports and statistics

---

## Security & Performance

### Security
- **Auth**: Zend_Auth + MD5 passwords (MD5 was standard in 2007!)
- **Roles**: Stored in DB (a/e/u) but never implemented/checked
- **Input Validation**: Zend_Form filtering
- **Session**: Zend_Session management

### Performance
- **Limitations**: MyISAM table locking, single-server deployment
- **Scalability**: Vertical scaling only

---

## Development & Deployment

### Evolution Timeline
- **2007**: PHP 5 alpha + ZF pre-1.0 + manual FTP deployment + include_path setup
- **2020+**: Modernized to PHP 8.3 + Composer + Docker + PSR-4 autoloading

### Current Setup
- **Dependencies**: Composer-managed
- **Container**: Docker with PHP 8.3 + Apache
- **Database**: MariaDB 11.1 (MyISAM)
- **Interface**: Traditional web only (no REST APIs)

---

## Limitations & Debt

### Technical Issues
- **Framework**: ZF1 (EOL, using ZF1 Future fork)
- **Database**: MyISAM (no transactions, table-level locking)
- **Security**: MD5 passwords, no role-based access control
- **Architecture**: Monolithic design, tight coupling
- **Code Patterns**: Classic rookie mistakes (e.g., DELETE in foreach loops instead of bulk operations)

### Usage Pattern
- **Educational**: Actually used by teachers for student mock tests (real-world validation)
- **Maintenance**: Manual database management, occasional updates
- **Scale**: Limited to single-server deployment

---

## Conclusion

Testy represents a time capsule from 2007 web development. Originally created as a college graduation project by a junior developer (2-3 years experience) during PHP 5 alpha era with pre-1.0 Zend Framework. The code shows typical rookie mistakes and naive patterns from when web best practices were still emerging.

**Historical Context:**
- **Builder**: Junior developer transitioning from PHP 4/MySQL 3 to PHP 5 alpha
- **Era**: 2007 web - limited documentation, emerging best practices
- **Framework**: Started with ZF 0.7/0.8, before 1.0 release
- **Reality**: Core functionality prioritized over polish/authorization

**Technical Evolution:**
- **2007**: Manual dependency management, FTP deployment, include_path configuration
- **2020+**: Composer integration, Docker containerization, PSR-4 autoloading
- **Current**: PHP 8.3 compatibility while maintaining original architecture

The project provides an authentic example of legacy system modernization - not a theoretical greenfield application, but a real system that has grown with the ecosystem. This makes it perfect for demonstrating incremental transformation techniques while preserving functional capabilities.

---

**Document Status:** Complete analysis spanning 18 years of technical evolution
**Historical Context**: From PHP 5 alpha graduation project to modern educational showcase
**Current Purpose**: Authentic legacy system for modernization demonstrations