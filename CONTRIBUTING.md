# Contributing to ArcheryPro

## Branch Strategy (Git Flow)

| Prefix | Kegunaan | Contoh |
|--------|----------|--------|
| main | Production, protected | main |
| develop | Integration branch | develop |
| feature/ | Fitur baru | feature/PROJ-142-oauth-google |
| bugfix/ | Bug non-urgent | bugfix/PROJ-201-score-rounding |
| hotfix/ | Bug urgent dari main | hotfix/PROJ-300-login-down |
| release/ | Persiapan rilis | release/v1.1.0 |
| support/ | Maintenance versi lama | support/v1.x |
| chore/ | Maintenance | chore/update-deps |
| docs/ | Dokumentasi | docs/api-endpoints |
| test/ | Testing | test/scoring-service |
| ci/ | CI/CD | ci/github-actions |
| perf/ | Optimasi | perf/leaderboard-query |
| refactor/ | Restrukturisasi | refactor/auth-service |
| experiment/ | POC | experiment/websocket-live |
| spike/ | Riset teknis | spike/pdf-render-engine |

Format: <prefix>/<ISSUE-ID>-<slug-kebab-case>

## Commit Convention

Conventional Commits: <type>(<scope>): <subject>

Type: feat, fix, docs, style, refactor, perf, test, build, ci, chore, revert

Format lengkap:

    <type>(<scope>): <subject max 72 char>

    <body: WHY, bukan WHAT>

    <footer: Refs / Breaking Changes>

## Pre-Push Checklist

- [ ] git diff --cached --check bersih
- [ ] Tidak ada .env, vendor/, node_modules/ ter-tracked
- [ ] Commit message pakai Conventional Commits
- [ ] Branch name sesuai prefix
- [ ] Test lokal lulus
