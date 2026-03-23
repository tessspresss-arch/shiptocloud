import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const currentFilePath = fileURLToPath(import.meta.url);
const workspaceRoot = path.resolve(path.dirname(currentFilePath), '..', '..');
const fixtureCache = new Map<string, unknown>();

export function ensurePhpFixture<T>(scriptFile: string): T {
  if (fixtureCache.has(scriptFile)) {
    return fixtureCache.get(scriptFile) as T;
  }

  const scriptPath = path.join(workspaceRoot, 'scripts', scriptFile);
  const phpBinary = process.env.PHP_BINARY ?? 'php';
  const rawOutput = execFileSync(phpBinary, [scriptPath], {
    cwd: workspaceRoot,
    encoding: 'utf8',
  }).trim();

  const payload = JSON.parse(rawOutput) as T;
  fixtureCache.set(scriptFile, payload);

  return payload;
}

export function runPhpJsonScript<T>(scriptFile: string, args: string[] = []): T {
  const scriptPath = path.join(workspaceRoot, 'scripts', scriptFile);
  const phpBinary = process.env.PHP_BINARY ?? 'php';
  const rawOutput = execFileSync(phpBinary, [scriptPath, ...args], {
    cwd: workspaceRoot,
    encoding: 'utf8',
  }).trim();

  return JSON.parse(rawOutput) as T;
}
