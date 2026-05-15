# Epic 2: Authentication & Authorization
**Status:** 🔴 Not Started | **Priority:** P0 (MVP Blocker)  
**Estimated Tasks:** 16 | **Dependencies:** Epic 1

## Goal
Complete authentication system with roles, OAuth, email verification, password reset, 2FA Passkey, and Laravel authorization policies.

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
- [ ] User can register with valid data
- [ ] User can login with correct credentials
- [ ] User can reset password via email link
- [ ] Email verification required before commenting
- [ ] CSRF protection active on all forms
- [ ] Rate limiting: max 5 login attempts/minute

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
- [ ] Author can only edit their own articles
- [ ] Editor can edit any article and publish
- [ ] Admin can do everything including role assignment
- [ ] Regular user can only comment and bookmark
- [ ] Guest can only read published articles

---

### 2.3: OAuth Social Login
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T2.3.1 | Configure Socialite for GitHub & Google | Add to config/services.php, set env vars |
| T2.3.2 | Create SocialiteController | Redirect to provider, handle callback, find-or-create user, login |
| T2.3.3 | Add OAuth buttons to login/register pages | "Login with GitHub" and "Login with Google" buttons |

**Acceptance Criteria:**
- [ ] User can login with GitHub account
- [ ] User can login with Google account
- [ ] First OAuth login auto-creates user record
- [ ] OAuth user can set password later if needed

---

### 2.4: Passkey/WebAuthn 2FA
**Priority:** P0 (Mandatory for admin) | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T2.4.1 | Configure WebAuthn in Laravel 13 | Enable passkey authentication in config/auth.php |
| T2.4.2 | Create 2FA setup page in user profile | Register passkey, test passkey, remove passkey |
| T2.4.3 | Add 2FA challenge to admin login | Require passkey verification after password for admin accounts |

**Acceptance Criteria:**
- [ ] Admin can register a passkey (fingerprint/security key)
- [ ] Admin login requires passkey verification
- [ ] Passkey can be removed and re-registered

---

**Epic Completion Criteria:**
- [ ] Complete auth flow: register → verify email → login → access dashboard
- [ ] OAuth login works for GitHub and Google
- [ ] Role-based access control enforced via policies
- [ ] Admin 2FA passkey functional
- [ ] All auth forms have proper error/loading/empty states
