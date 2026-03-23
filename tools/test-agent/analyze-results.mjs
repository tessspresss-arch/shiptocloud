import fs from 'node:fs';
import path from 'node:path';

const reportsDir = path.resolve('storage/test-reports');
const phpunitReport = path.join(reportsDir, 'phpunit.junit.xml');
const playwrightReport = path.join(reportsDir, 'playwright-results.json');

fs.mkdirSync(reportsDir, { recursive: true });

function readFileSafe(filePath) {
  try {
    return fs.readFileSync(filePath, 'utf8');
  } catch {
    return null;
  }
}

function parsePhpUnit(xmlText) {
  if (!xmlText) {
    return {
      available: false,
      tests: 0,
      failures: 0,
      errors: 0,
      skipped: 0,
      failedCases: [],
    };
  }

  const firstSuiteTag = xmlText.match(/<testsuite\b([^>]*)>/i);
  const suiteAttrs = parseXmlAttributes(firstSuiteTag?.[1] ?? '');
  const tests = Number(suiteAttrs.tests ?? 0);
  const failures = Number(suiteAttrs.failures ?? 0);
  const errors = Number(suiteAttrs.errors ?? 0);
  const skipped = Number(suiteAttrs.skipped ?? 0);

  const failedCases = [];
  const testCaseRegex = /<testcase\b((?:(?!\/>).)*)>([\s\S]*?)<\/testcase>/gi;
  let match;
  while ((match = testCaseRegex.exec(xmlText)) !== null) {
    const [, attrsRaw, body] = match;
    const attrs = parseXmlAttributes(attrsRaw);
    const classname = attrs.classname || attrs.class || 'backend';
    const name = attrs.name || 'testcase';

    if (/<failure[\s>]|<error[\s>]/i.test(body)) {
      const messageMatch =
        body.match(/<(failure|error)[^>]*message="([^"]*)"/i)
        || body.match(/<(failure|error)[^>]*>([\s\S]*?)<\/(failure|error)>/i);

      failedCases.push({
        suite: classname,
        title: name,
        message: (messageMatch?.[2] ?? 'Echec backend').trim(),
      });
    }
  }

  return {
    available: true,
    tests,
    failures,
    errors,
    skipped,
    failedCases,
  };
}

function parseXmlAttributes(input) {
  const attrs = {};
  const attrRegex = /([\w:-]+)="([^"]*)"/g;
  let m;
  while ((m = attrRegex.exec(input)) !== null) {
    attrs[m[1]] = m[2];
  }
  return attrs;
}

function flattenPlaywrightSuites(suites = [], filePrefix = '') {
  const failed = [];

  const walk = (nodes, prefix) => {
    for (const node of nodes || []) {
      const nextPrefix = prefix ? `${prefix} > ${node.title}` : node.title;

      if (Array.isArray(node.specs) && node.specs.length > 0) {
        for (const spec of node.specs) {
          const fullTitle = `${nextPrefix} > ${spec.title}`;
          for (const test of spec.tests || []) {
            const result = test.results?.find((r) => r.status && r.status !== 'skipped') || test.results?.[0];
            const status = result?.status || 'unknown';
            if (status === 'failed' || status === 'timedOut') {
              failed.push({
                file: filePrefix || node.file || '',
                title: fullTitle,
                status,
                message: result?.error?.message || 'Echec E2E',
              });
            }
          }
        }
      }

      if (Array.isArray(node.suites) && node.suites.length > 0) {
        walk(node.suites, nextPrefix);
      }
    }
  };

  walk(suites, '');
  return failed;
}

function parsePlaywright(jsonText) {
  if (!jsonText) {
    return {
      available: false,
      total: 0,
      failed: 0,
      failedCases: [],
    };
  }

  let parsed;
  try {
    parsed = JSON.parse(jsonText);
  } catch {
    return {
      available: false,
      total: 0,
      failed: 0,
      failedCases: [],
    };
  }

  const failedCases = flattenPlaywrightSuites(parsed.suites || []);

  let total = 0;
  const countTests = (suites = []) => {
    for (const suite of suites) {
      for (const spec of suite.specs || []) {
        total += (spec.tests || []).length;
      }
      countTests(suite.suites || []);
    }
  };
  countTests(parsed.suites || []);

  return {
    available: true,
    total,
    failed: failedCases.length,
    failedCases,
  };
}

function classifySeverity(title, source) {
  const text = `${title} ${source}`.toLowerCase();
  const criticalTokens = ['auth', 'login', 'permission', 'facture', 'billing', 'paiement', 'salle', 'waiting', 'rdv', 'rendez'];
  const highTokens = ['patient', 'consultation', 'planning', 'agenda', 'dashboard', 'sms'];

  if (criticalTokens.some((token) => text.includes(token))) {
    return 'critical';
  }
  if (highTokens.some((token) => text.includes(token))) {
    return 'high';
  }
  return 'medium';
}

const phpunit = parsePhpUnit(readFileSafe(phpunitReport));
const playwright = parsePlaywright(readFileSafe(playwrightReport));

const findings = [
  ...phpunit.failedCases.map((test) => ({
    source: 'phpunit',
    suite: test.suite,
    title: test.title,
    severity: classifySeverity(test.title, test.suite),
    message: test.message,
  })),
  ...playwright.failedCases.map((test) => ({
    source: 'playwright',
    suite: test.file,
    title: test.title,
    severity: classifySeverity(test.title, test.file),
    message: test.message,
  })),
];

const severityOrder = { critical: 1, high: 2, medium: 3 };
findings.sort((a, b) => severityOrder[a.severity] - severityOrder[b.severity]);

const summary = {
  generated_at: new Date().toISOString(),
  status: findings.length > 0 ? 'failed' : 'passed',
  backend: {
    available: phpunit.available,
    tests: phpunit.tests,
    failures: phpunit.failures + phpunit.errors,
    skipped: phpunit.skipped,
  },
  e2e: {
    available: playwright.available,
    tests: playwright.total,
    failures: playwright.failed,
  },
  findings,
};

const mdLines = [];
mdLines.push('# Medisys Pro - Rapport Agent IA');
mdLines.push('');
mdLines.push(`- Statut global: **${summary.status.toUpperCase()}**`);
mdLines.push(`- Generation: ${summary.generated_at}`);
mdLines.push('');
mdLines.push('## Resultats backend (PHPUnit)');
mdLines.push(`- Disponible: ${summary.backend.available ? 'Oui' : 'Non'}`);
mdLines.push(`- Tests: ${summary.backend.tests}`);
mdLines.push(`- Echecs: ${summary.backend.failures}`);
mdLines.push(`- Ignores: ${summary.backend.skipped}`);
mdLines.push('');
mdLines.push('## Resultats navigateur (Playwright)');
mdLines.push(`- Disponible: ${summary.e2e.available ? 'Oui' : 'Non'}`);
mdLines.push(`- Tests: ${summary.e2e.tests}`);
mdLines.push(`- Echecs: ${summary.e2e.failures}`);
mdLines.push('');
mdLines.push('## Findings classes par gravite');
if (findings.length === 0) {
  mdLines.push('- Aucun bug detecte par les tests executes.');
} else {
  findings.forEach((item, index) => {
    mdLines.push(`${index + 1}. [${item.severity.toUpperCase()}] (${item.source}) ${item.title}`);
    mdLines.push(`   - Suite/Fichier: ${item.suite || 'n/a'}`);
    mdLines.push(`   - Detail: ${item.message}`);
  });
}

const htmlRows = findings
  .map(
    (f) => `<tr><td>${f.severity.toUpperCase()}</td><td>${f.source}</td><td>${escapeHtml(
      f.title
    )}</td><td>${escapeHtml(f.suite || 'n/a')}</td><td>${escapeHtml(f.message || '')}</td></tr>`
  )
  .join('\n');

const html = `<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Medisys Pro - Rapport Agent IA</title>
  <style>
    body{font-family:Segoe UI,Arial,sans-serif;background:#f4f8fc;color:#123;padding:20px}
    .card{background:#fff;border:1px solid #d6e2ef;border-radius:12px;padding:16px;margin-bottom:16px}
    h1{margin:0 0 12px}
    table{width:100%;border-collapse:collapse}
    th,td{border:1px solid #dbe6f2;padding:8px;text-align:left;vertical-align:top}
    th{background:#eef5fd}
  </style>
</head>
<body>
  <div class="card">
    <h1>Medisys Pro - Rapport Agent IA</h1>
    <p><strong>Statut:</strong> ${summary.status.toUpperCase()}</p>
    <p><strong>Generation:</strong> ${summary.generated_at}</p>
    <p><strong>Backend:</strong> ${summary.backend.tests} tests, ${summary.backend.failures} echecs</p>
    <p><strong>E2E:</strong> ${summary.e2e.tests} tests, ${summary.e2e.failures} echecs</p>
  </div>
  <div class="card">
    <h2>Findings</h2>
    <table>
      <thead>
        <tr>
          <th>Gravite</th>
          <th>Source</th>
          <th>Test</th>
          <th>Suite/Fichier</th>
          <th>Message</th>
        </tr>
      </thead>
      <tbody>
        ${htmlRows || '<tr><td colspan="5">Aucun bug detecte.</td></tr>'}
      </tbody>
    </table>
  </div>
</body>
</html>`;

fs.writeFileSync(path.join(reportsDir, 'agent-summary.json'), JSON.stringify(summary, null, 2));
fs.writeFileSync(path.join(reportsDir, 'agent-report.md'), `${mdLines.join('\n')}\n`);
fs.writeFileSync(path.join(reportsDir, 'agent-report.html'), html);

console.log(`[AI Test Agent] Rapport genere: ${path.join(reportsDir, 'agent-report.html')}`);

function escapeHtml(value) {
  return String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}
