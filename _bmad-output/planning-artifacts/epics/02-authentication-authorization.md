# Epic 2: Authentication & Authorization
**Status:** 🟢 Completed | **Priority:** P0 (MVP Blocker)  
**Estimated Tasks:** 16 | **Dependencies:** Epic 1

## Goal
Complete authentication system with roles, OAuth, email verification, password reset, production-enforced app-based MFA, and Laravel authorization policies.

## Stories

### 2.1: Core Authentication
**Priority:** P0 | **Estimate:** 5 tasks

| # | Task | Description |
|---|---|---|
| T2.1.1 | Install & configure Laravel auth scaffolding | Use `php artisan make:auth` or manual setup with Livewire components for login, register, forgot password |
| T2.1.2 | Create Login Livewire page | Email/password form, validation, remember me, rate limiting, redirect to intended |
| T2.1.3 | Create Register Livewire page | Name, email, username, password confirmation, validation, auto-login after register |
| T2.1.4 | Create Forgot/Reset Password flow | Email form → send reset link → reset password form with token validation |
| T2.1.5 | Create Email Verification flow | Send verification email on register, verify route, middleware `verified` |

**Acceptance Criteria:**
- [x] User can register with valid data
- [x] User can login with correct credentials
- [x] User can reset password via email link
- [x] Email verification required before commenting
- [x] CSRF protection active on all forms
- [x] Rate limiting: max 5 login attempts/minute

---

### 2.2: Role & Authorization System
**Priority:** P0 | **Estimate:** 5 tasks

| # | Task | Description |
|---|---|---|
| T2.2.1 | Create ArticlePolicy | view, viewAny, create, update, delete, publish, restore methods |
| T2.2.2 | Create CommentPolicy | create, update, delete, approve, reject methods |
| T2.2.3 | Create UserPolicy | viewAny, view, update, delete, assignRole methods |
| T2.2.4 | Register policies in AuthServiceProvider | Map models to policies |
| T2.2.5 | Create role middleware | `role:admin,editor` middleware for Filament and route protection |

**Acceptance Criteria:**
- [x] Author can only edit their own articles
- [x] Editor can edit any article and publish
- [x] Admin can do everything including role assignment
- [x] Regular user can only comment and bookmark
- [x] Guest can only read published articles

---

### 2.3: OAuth Social Login
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T2.3.1 | Configure Socialite for GitHub & Google | Add to config/services.php, set env vars |
| T2.3.2 | Create SocialiteController | Redirect to provider, handle callback, find-or-create user, login |
| T2.3.3 | Add OAuth buttons to login/register pages | "Login with GitHub" and "Login with Google" buttons |

**Acceptance Criteria:**
- [x] User can login with GitHub account
- [x] User can login with Google account
- [x] First OAuth login auto-creates user record
- [x] OAuth user can set password later if needed

---

### 2.4: App-Based MFA
**Priority:** P0 (Mandatory for admin) | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T2.4.1 | Configure Filament app authentication | Enable app-based MFA and recovery codes on the backoffice panel |
| T2.4.2 | Create 2FA setup flow in profile | Register authenticator app, verify challenge, manage recovery codes |
| T2.4.3 | Require MFA for production admin access | Enforce MFA before admin accounts can use the backoffice in production |

**Acceptance Criteria:**
- [x] Admin can register an authenticator app and recovery codes
- [x] Production admin access requires MFA verification
- [x] MFA enrollment can be reset and reconfigured

---

**Epic Completion Criteria:**
- [x] Complete auth flow: register → verify email → login → access dashboard
- [x] OAuth login works for GitHub and Google
- [x] Role-based access control enforced via policies
- [x] Admin MFA flow functional
- [x] All auth forms have proper error/loading/empty states
