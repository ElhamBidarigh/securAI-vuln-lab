#!/usr/bin/env python3
"""
SecurAI — Report Builder
Combines Gitleaks + Semgrep + dependency audit results
into a single beautiful HTML report.
"""
import json
import os
import html
from datetime import datetime
from pathlib import Path

REPORT_DIR = Path("security-report")
REPORT_DIR.mkdir(exist_ok=True)

def load_json(path):
    p = Path(path)
    if not p.exists():
        return None
    try:
        return json.loads(p.read_text())
    except Exception:
        return None

def collect_findings():
    findings = []

    # ── Gitleaks ─────────────────────────────────────
    gl = load_json("gitleaks-report.json")
    if gl and isinstance(gl, list):
        for f in gl:
            findings.append({
                "source": "Gitleaks",
                "severity": "critical",
                "category": "Secrets",
                "title": f.get("Description", "Hardcoded secret"),
                "file": f.get("File", "—"),
                "line": f.get("StartLine", "—"),
                "detail": f.get("Match", "")[:200],
            })

    # ── Semgrep ──────────────────────────────────────
    sg = load_json("semgrep.sarif")
    if sg and "runs" in sg:
        for run in sg["runs"]:
            rules = {r["id"]: r for r in run.get("tool", {}).get("driver", {}).get("rules", [])}
            for r in run.get("results", []):
                rid = r.get("ruleId", "unknown")
                rule = rules.get(rid, {})
                level = r.get("level", rule.get("defaultConfiguration", {}).get("level", "warning"))
                sev = "critical" if level == "error" else "high" if level == "warning" else "low"
                loc = r.get("locations", [{}])[0].get("physicalLocation", {})
                findings.append({
                    "source": "Semgrep",
                    "severity": sev,
                    "category": rule.get("metadata", {}).get("category", "SAST"),
                    "title": rule.get("shortDescription", rid),
                    "file": loc.get("artifactLocation", {}).get("uri", "—"),
                    "line": loc.get("region", {}).get("startLine", "—"),
                    "detail": (rule.get("fullDescription") or rule.get("shortDescription") or "")[:300],
                })

    # ── Composer audit ───────────────────────────────
    ca = load_json("composer-audit.json")
    if ca and "advisories" in ca:
        for pkg, advs in ca["advisories"].items():
            for adv in advs:
                findings.append({
                    "source": "Composer",
                    "severity": adv.get("severity", "high").lower(),
                    "category": "Dependencies",
                    "title": f"{pkg}: {adv.get('title', 'Vulnerable dependency')}",
                    "file": "composer.lock",
                    "line": "—",
                    "detail": adv.get("cve", "") + " — " + adv.get("link", ""),
                })

    # ── npm audit ────────────────────────────────────
    na = load_json("npm-audit.json")
    if na and "vulnerabilities" in na:
        for pkg, v in na["vulnerabilities"].items():
            findings.append({
                "source": "npm audit",
                "severity": v.get("severity", "high").lower(),
                "category": "Dependencies",
                "title": f"{pkg}: {v.get('title', 'Vulnerable dependency')}",
                "file": "package-lock.json",
                "line": "—",
                "detail": v.get("url", ""),
            })

    return findings

def render_html(findings):
    counts = {"critical": 0, "high": 0, "medium": 0, "low": 0}
    for f in findings:
        s = f["severity"].lower()
        if s in counts:
            counts[s] += 1

    rows = ""
    for f in findings:
        sev_class = f["severity"].lower()
        rows += f"""
        <tr class="row-{sev_class}">
          <td><span class="sev sev-{sev_class}">{f["severity"].upper()}</span></td>
          <td>{html.escape(str(f["source"]))}</td>
          <td>{html.escape(str(f["category"]))}</td>
          <td>
            <div class="title">{html.escape(str(f["title"]))}</div>
            <div class="detail">{html.escape(str(f["detail"]))}</div>
          </td>
          <td><code>{html.escape(str(f["file"]))}:{html.escape(str(f["line"]))}</code></td>
        </tr>
        """

    if not findings:
        rows = '<tr><td colspan="5" class="clean">✅ No findings. Nice work.</td></tr>'

    return f"""<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SecurAI Security Report — {datetime.utcnow().strftime('%Y-%m-%d')}</title>
<style>
  :root{{--bg:#070b14;--surface:#0f1626;--border:#1f2b45;--text:#e6edf7;--muted:#8b9bb4;
        --crit:#ef4444;--high:#f59e0b;--med:#3b82f6;--low:#64748b;--ok:#10b981}}
  *{{box-sizing:border-box}}
  body{{font-family:'Inter',system-ui,sans-serif;background:var(--bg);color:var(--text);
       margin:0;padding:40px 20px;line-height:1.6}}
  .wrap{{max-width:1100px;margin:0 auto}}
  h1{{font-size:32px;letter-spacing:-.8px;margin:0 0 6px}}
  .sub{{color:var(--muted);font-size:14px;margin-bottom:28px}}
  .cards{{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:30px}}
  .card{{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:18px}}
  .card .n{{font-size:28px;font-weight:800}}
  .card .l{{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px}}
  .n-crit{{color:var(--crit)}}.n-high{{color:var(--high)}}.n-med{{color:var(--med)}}.n-low{{color:var(--low)}}
  table{{width:100%;border-collapse:collapse;background:var(--surface);
         border:1px solid var(--border);border-radius:12px;overflow:hidden}}
  th{{text-align:left;padding:12px 14px;font-size:11px;text-transform:uppercase;
      letter-spacing:.6px;color:var(--muted);border-bottom:1px solid var(--border);background:#0a0f1c}}
  td{{padding:14px;border-bottom:1px solid var(--border);font-size:13.5px;vertical-align:top}}
  .row-critical{{background:rgba(239,68,68,.04)}}
  .sev{{padding:3px 9px;border-radius:5px;font-size:10.5px;font-weight:700}}
  .sev-critical{{background:rgba(239,68,68,.15);color:#fca5a5}}
  .sev-high{{background:rgba(245,158,11,.15);color:#fcd34d}}
  .sev-medium,.sev-med{{background:rgba(59,130,246,.15);color:#93c5fd}}
  .sev-low{{background:rgba(100,116,139,.15);color:#cbd5e1}}
  .title{{font-weight:600;margin-bottom:4px}}
  .detail{{color:var(--muted);font-size:12.5px;word-break:break-word}}
  code{{background:#0a0f1c;padding:2px 6px;border-radius:4px;font-size:12px;color:#a5f3d0}}
  .clean{{text-align:center;padding:40px;color:var(--ok)}}
  .footer{{margin-top:30px;text-align:center;color:var(--muted);font-size:12.5px}}
</style>
</head>
<body>
  <div class="wrap">
    <h1>🛡️ SecurAI Security Report</h1>
    <div class="sub">Generated {datetime.utcnow().strftime('%Y-%m-%d %H:%M UTC')} · {len(findings)} finding(s)</div>

    <div class="cards">
      <div class="card"><div class="n n-crit">{counts['critical']}</div><div class="l">Critical</div></div>
      <div class="card"><div class="n n-high">{counts['high']}</div><div class="l">High</div></div>
      <div class="card"><div class="n n-med">{counts['medium']}</div><div class="l">Medium</div></div>
      <div class="card"><div class="n n-low">{counts['low']}</div><div class="l">Low</div></div>
    </div>

    <table>
      <thead>
        <tr><th>Severity</th><th>Source</th><th>Category</th><th>Finding</th><th>Location</th></tr>
      </thead>
      <tbody>{rows}</tbody>
    </table>

    <div class="footer">
      SecurAI · Automated scan · <a href="https://elhambidarigh.github.io/securai/"
      style="color:var(--ok)">Interactive audit tool</a>
    </div>
  </div>
</body>
</html>
"""

def main():
    findings = collect_findings()
    html_out = render_html(findings)
    (REPORT_DIR / "index.html").write_text(html_out, encoding="utf-8")

    # Summary for GitHub step summary
    summary = f"## 🛡️ SecurAI Scan\n\n"
    summary += f"**Total findings:** {len(findings)}\n\n"
    counts = {}
    for f in findings:
        counts[f["severity"]] = counts.get(f["severity"], 0) + 1
    for sev in ["critical", "high", "medium", "low"]:
        if counts.get(sev):
            summary += f"- {sev.upper()}: {counts[sev]}\n"
    Path("summary.md").write_text(summary)

if __name__ == "__main__":
    main()
