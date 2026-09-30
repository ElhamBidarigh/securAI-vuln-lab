# 🔬 SecurAI Vuln Lab

A deliberately vulnerable PHP/MySQL application built to demonstrate
the 5 most common OWASP Top 10 vulnerabilities — and their fixes.

> ⚠️ **WARNING: This app is intentionally insecure.**
> It exists **only** for education and portfolio purposes.
> Never deploy `vuln-app/` on any internet-facing server.
> Use Docker in an isolated environment.

## What's inside

| # | Vulnerability | OWASP | File |
|---|---|---|---|
| 1 | SQL Injection | A03:2021 | `vuln-app/login.php` |
| 2 | Plain-text password storage | A02:2021 | `vuln-app/register.php` |
| 3 | IDOR (Insecure Direct Object Reference) | A01:2021 | `vuln-app/profile.php` |
| 4 | Stored XSS | A03:2021 | `vuln-app/comment.php` |
| 5 | Unrestricted File Upload | A04:2021 | `vuln-app/upload.php` |

Every vulnerability has:
- ✅ A working exploit script in `tests/exploit.sh`
- ✅ A documented finding in `report/SECURITY-REPORT.md`
- ✅ A fixed version in `secure-app/`

## Quick start

```bash
git clone https://github.com/ElhamBidarigh/securAI-vuln-lab.git
cd securAI-vuln-lab
docker compose up -d
