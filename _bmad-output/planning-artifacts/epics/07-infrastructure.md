# Epic 7: Infrastructure — Docker, Security, CI/CD, Horizon
**Status:** 🟢 Completed | **Priority:** P0 (MVP Blocker)  
**Estimated Tasks:** 16 | **Dependencies:** Epic 4

## Goal
Production-ready Docker setup, security hardening, CI/CD pipeline, and FrankenPHP Octane configuration.

## Stories

### 7.1: Docker Setup
**Priority:** P0 | **Estimate:** 5 tasks

| # | Task | Description |
|---|---|---|
| T7.1.1 | Create multi-stage Dockerfile | Build stage (composer + bun + vite build), production stage (frankenphp, extensions, non-root user) |
| T7.1.2 | Create docker-compose.yml | Services: app (frankenphp worker), mysql 8.4, redis 7, queue worker, scheduler |
| T7.1.3 | Add healthchecks | MySQL healthcheck, Redis healthcheck, app healthcheck endpoint |
| T7.1.4 | Create .dockerignore | Exclude node_modules, vendor (build from scratch), .git, tests, docs |
| T7.1.5 | Configure FrankenPHP worker mode | ENV FRANKENPHP_CONFIG, Octane config with max_requests, worker count |

**Acceptance Criteria:**
- [x] `docker compose up` starts all services
- [x] Application accessible at http://localhost
- [x] All healthchecks pass
- [x] Queue worker processes jobs
- [x] Non-root user runs the application

---

### 7.2: Security Hardening
**Priority:** P0 | **Estimate:** 5 tasks

| # | Task | Description |
|---|---|---|
| T7.2.1 | Create SecurityHeaders middleware | CSP, HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy |
| T7.2.2 | Configure rate limiting | Per-route limits: login (5/min), register (3/hr), search (30/min), comment (5/min), API (60/min) |
| T7.2.3 | Implement file upload security | MIME type validation, max file size, random filenames, server-side resize |
| T7.2.4 | Configure CORS | Restrict to production domain, allow only necessary methods and headers |
| T7.2.5 | Add security headers to Filament | Same security headers for admin panel routes |

**Acceptance Criteria:**
- [x] All security headers present in HTTP responses
- [x] Rate limiting returns 429 with Retry-After
- [x] File upload rejects non-image files
- [x] CORS blocks unauthorized origins
- [x] Security headers verified with securityheaders.com

---

### 7.3: FrankenPHP & Octane Configuration
**Priority:** P0 | **Estimate:** 2 tasks

| # | Task | Description |
|---|---|---|
| T7.3.1 | Configure Octane for FrankenPHP | Set server, worker count, max requests, watch paths for hot reload |
| T7.3.2 | Verify Octane compatibility | Ensure no static property accumulation, singletons use `scoped` where needed |

**Acceptance Criteria:**
- [x] `php artisan octane:frankenphp` starts without errors
- [x] Worker mode handles concurrent requests
- [x] No memory leaks after 500 requests
- [x] `composer run dev` works with all concurrent processes

### 7.4: Laravel Horizon Setup
**Priority:** P0 | **Estimate:** 2 tasks

| # | Task | Description |
|---|---|---|
| T7.4.1 | Configure HorizonServiceProvider gate | Restrict `/horizon` to admin role, configure per-queue worker balancing |
| T7.4.2 | Configure Horizon environment | Queue worker counts, memory limits, trim/supervisor settings in `config/horizon.php` |

**Acceptance Criteria:**
- [x] Horizon dashboard accessible at `/horizon` for admin
- [x] Non-admin gets 403 at `/horizon`
- [x] Real-time queue metrics visible (jobs/min, failed jobs, throughput)
- [x] Worker balancing configured: emails=2, images=1, default=3

---

### 7.4: CI/CD Pipeline
**Priority:** P0 | **Estimate:** 2 tasks

| # | Task | Description |
|---|---|---|
| T7.4.1 | Create GitHub Actions CI workflow | PHP 8.5, MySQL service, Redis service, composer install, pint check, pest tests, vite build |
| T7.4.2 | Create deploy script | SSH into server, git pull, docker compose up --build -d, migrate, cache |

**Acceptance Criteria:**
- [x] CI runs on every push and PR
- [x] Pint fails if code style issues
- [x] Pest fails if tests fail
- [x] Build step produces production assets

---

**Epic Completion Criteria:**
- [x] `docker compose up` deploys full stack
- [x] Security headers score A+ on security scanning
- [x] Octane worker mode functional
- [x] CI pipeline green on main branch
