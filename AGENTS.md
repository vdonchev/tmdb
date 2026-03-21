# AGENTS.md
Guidance for coding agents working in this Symfony 8 movie app.

## Scope and priorities
1. Keep changes minimal, local, and consistent with existing patterns.
2. Prefer fixing root causes over adding workarounds.
3. Do not introduce new tooling (linters/formatters/frameworks) unless asked.
4. Do not modify secrets or committed env files with sensitive values.

## Repository snapshot
- Stack: PHP 8.4, Symfony 8, Twig, Asset Mapper, Stimulus, Turbo, Tailwind v4, Flowbite.
- Package managers: Composer + npm.
- Tests: PHPUnit 12 via `phpunit.dist.xml`.
- Deploy: Deployer recipe in `deploy.yaml`.
- App cache pool used in repositories/services: `tv.cache`.

## Cursor/Copilot rules
- No `.cursor/rules/`, `.cursorrules`, or `.github/copilot-instructions.md` found.
- If any appear later, treat them as higher-priority repository instructions.

## Setup
Run from repo root:
```bash
composer install
npm install
```
Environment notes:
- Defaults live in `.env`.
- Use `.env.local` for local overrides.
- PHPUnit forces `APP_ENV=test` (see `phpunit.dist.xml`).

## Build commands
Deploy build flow:
```bash
php bin/console importmap:install
php bin/console tailwind:build --minify
php bin/console asset-map:compile
```
Useful Tailwind variants:
```bash
php bin/console tailwind:build --watch
php bin/console tailwind:build --minify
```
Production deploy:
```bash
php vendor/bin/dep deploy prod
```

## Deploy request workflow
When the user asks to "deploy" (or "deply"), follow this exact order:
1. Check local git changes (`git status`, staged/unstaged diff).
2. Group changes into logical commits (one or multiple commits as appropriate).
3. Push commits to remote.
4. Deploy with Deployer from vendor:
```bash
php vendor/bin/dep deploy prod
```
Assume a deploy request includes permission to commit, push, and deploy in this sequence.

## Lint and validation commands
There is no dedicated PHP-CS-Fixer/PHPStan/ESLint config in this repo.
Use Symfony-native checks:
```bash
php bin/console lint:container
php bin/console lint:yaml config
php bin/console lint:twig templates
php bin/console lint:translations translations
php bin/console doctrine:schema:validate
```
If you changed services/routes/config, run at least:
```bash
php bin/console lint:container
php bin/console lint:yaml config
```

## Test commands
Run all tests:
```bash
php bin/phpunit
```
List suites/tests:
```bash
php bin/phpunit --list-suites
php bin/phpunit --list-tests
```
Run one file:
```bash
php bin/phpunit tests/Path/To/SomeTest.php
```
Run one method by filter:
```bash
php bin/phpunit --filter testMethodName
```
Run one class + method (reliable pattern):
```bash
php bin/phpunit tests/Path/To/SomeTest.php --filter '/::testMethodName$/'
```
Stop fast during iteration:
```bash
php bin/phpunit --stop-on-failure
```
Note: `tests/` currently contains only bootstrap scaffolding; add tests under `tests/` with `*Test.php` suffix.

## PHP style guidelines
- Follow `.editorconfig`: UTF-8, LF, 4-space indent, final newline.
- Use one class per file under `App\` PSR-4 namespaces.
- Prefer constructor property promotion for injected dependencies and DTO fields.
- Prefer explicit scalar/object types for properties, args, and return types.
- Prefer `final` classes for services/factories/DTOs unless extension is required.
- Prefer `readonly` (class or properties) where data is immutable.
- Keep imports explicit (`use ...`), avoid inline FQCNs in method bodies.
- Keep multiline argument lists and signatures with trailing commas.
- Prefer short arrays `[]`, strict comparisons, and early returns.
- `declare(strict_types=1);` exists in part of the codebase; preserve local file style when editing, and prefer strict types in new PHP files.

## Naming conventions
- Controllers: `*Controller`, route names usually prefixed with `app_`.
- Repositories: `*Repository` (caching/orchestration boundary).
- Services: `*Service` or task-focused names (`ImdbScrapper`, API clients).
- Factories: `*Factory` (raw API payload -> DTO mapping).
- DTOs: `*Dto` with strong typing and minimal behavior.
- Filters: `*Filter` implementing `FilterInterface` for query serialization.
- Enums: singular domain names (`ListType`, `MovieSort`).

## Error handling guidelines
- At external HTTP boundaries, catch transport/client exceptions and map to domain exceptions (`TmdbApiException`, `KinocheckApiException`) or HTTP exceptions where appropriate.
- Catch broad `Throwable` only at boundaries, then rethrow domain-specific exceptions.
- Do not silently swallow exceptions unless returning an intentional fallback (`null`/`[]`) expected by callers.
- Keep controllers thin; move API parsing/cache logic into repositories/services.

## Dependency injection and configuration
- Rely on autowiring/autoconfigure defaults from `config/services.yaml`.
- Use attributes as established: `#[Route]`, `#[Autowire]`, `#[Target]`, serializer attributes.
- Keep external API values env-driven (`TMDB_*`), not hardcoded.
- Do not commit secrets or tokens.

## Caching conventions
- Caching is repository-centric with `CacheInterface` + `ItemInterface` callbacks.
- Keep cache keys deterministic (stable prefixes + identifiers).
- Always define TTL with `expiresAfter()`.
- Reuse `tv.cache` unless there is a clear reason to introduce another pool.

## Twig/frontend conventions
- Templates are grouped by feature under `templates/` (`home/`, `search/`, `_partials/`, `_turbo/`).
- Prefer translation keys (`translations/messages.*.yaml`) over hardcoded UI strings.
- Turbo frame endpoints under `_turbo/*` should validate Turbo-frame access where relevant.
- Use Tailwind utility classes and Flowbite components as the default UI approach.

## Stimulus/JS conventions
- Controllers live in `assets/controllers/*_controller.js`.
- Keep controllers focused on one responsibility.
- Use `static values`/`static targets` for DOM contracts.
- Initialize runtime behavior on `turbo:load` where needed.
- Follow local file style; avoid repo-wide JS reformatting.

## Practical change checklist
Backend changes:
1. `php bin/console lint:container`
2. `php bin/console lint:yaml config`
3. `php bin/phpunit` (or targeted test command)

Twig/frontend changes:
1. `php bin/console lint:twig templates`
2. `php bin/console tailwind:build --minify`
3. `php bin/console asset-map:compile`

Deploy-affecting changes:
1. `php bin/console importmap:install`
2. `php bin/console tailwind:build --minify`
3. `php bin/console asset-map:compile`
