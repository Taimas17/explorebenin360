# Phase 2: Transactions MVP — Auth (Sanctum), Offerings, Checkout (Paystack), Webhook, Bookings, Notifications, Admin/Provider Dashboards

## Summary
- Enable transactional bookings on ExploreBenin360 with secure payment processing and role-based access.
- Introduce Offerings and Bookings models, a Paystack checkout flow with secure webhook, and basic dashboards for travelers, providers, and admins.
- Keep scope MVP while aligning with existing branding and FR/EN i18n.

## Changes

### Backend (Laravel)
- Auth & Roles
  - Sanctum bearer token auth for SPA endpoints
  - Roles via spatie/permission: admin, provider, traveler
  - Seeded admin user and 3 providers; registration assigns traveler
  - Endpoints: POST /api/v1/auth/{register,login,logout}, GET /api/v1/auth/me
- Offerings (reservable)
  - Model/migration with fields per spec; public read-only endpoints
  - GET /api/v1/offerings, GET /api/v1/offerings/{slug}
- Bookings & Checkout
  - Model/migration with amount, currency, commission_amount, statuses
  - POST /api/v1/checkout/session (auth): capacity/date validation, compute amount, init Paystack, returns authorization_url + reference
  - GET /api/v1/bookings (owner list); GET /api/v1/bookings/{id}; POST /api/v1/bookings/{id}/cancel
- Paystack (sandbox)
  - App/Services/PaystackClient (initialize, signature verify)
  - Secure webhook POST /api/v1/payments/paystack/webhook verifying x-paystack-signature; idempotent charge.success → booking confirmed + commission
  - Callback UX: redirect to FRONTEND_URL/checkout/callback; server truth via webhook
- Notifications
  - Mailables BookingConfirmed / BookingCancelled (log driver by default)
- Admin/Provider endpoints (JSON)
  - Provider: GET /api/v1/provider/bookings (own offerings)
  - Admin: GET /api/v1/admin/bookings (filters), PATCH /api/v1/admin/bookings/{id}
- Config & CORS
  - config/payments.php with commission and Paystack settings; .env.example additions (PAYSTACK_*, COMMISSION_PERCENT, FRONTEND_URL)
  - CORS allows credentials and frontend origin
- Docs & Tests
  - OpenAPI updated with new endpoints
  - Feature test: login → checkout session → webhook success → booking confirmed
  - Unit test: Paystack signature verification

### Frontend (Vue 3 + Vite)
- Auth
  - Pinia auth store; axios Authorization header; Login, Register, Profile
  - Router guards for protected routes
- Offerings & Checkout
  - List/detail pages; “Réserver” CTA
  - Checkout form (dates/guests) → POST /checkout/session → redirect to Paystack
  - Callback page shows confirmation message
- Dashboards
  - Traveler: list and detail, cancel when allowed
  - Provider: list + earnings summary (gross, commission total, net)
  - Admin: list with per-row status change control (PATCH)
- i18n
  - FR/EN strings for new flows and dashboard labels

## Configuration
Backend .env:
- FRONTEND_ORIGIN=http://localhost:5173
- FRONTEND_URL=http://localhost:5173
- PAYSTACK_PUBLIC_KEY=pk_test_xxx
- PAYSTACK_SECRET_KEY=sk_test_xxx
- PAYSTACK_BASE_URL=https://api.paystack.co
- COMMISSION_PERCENT=12
- MAIL_MAILER=log

Frontend .env:
- VITE_API_BASE_URL=/api/v1
- VITE_PAYSTACK_PUBLIC_KEY=pk_test_xxx (only if inline widget is later used)

## Test plan
- Register or login; verify token stored and /auth/me returns user + roles
- Offerings visible publicly (list + detail)
- Start checkout: dates + guests; redirected to Paystack sandbox
- Simulate webhook: booking becomes confirmed; emails logged
- Traveler dashboard shows bookings; provider dashboard shows earnings; admin can update status

## Security & MVP notes
- Webhook signature verified (x-paystack-signature); idempotent handling
- Client callback not trusted for payment status; server is source of truth
- Email verification enabled on model; can be enforced before checkout in a follow-up
- Refund automation omitted (manual path documented)

## Acceptance criteria coverage
- Auth + roles seeded, offerings public, checkout flow with Paystack sandbox and callback page, secure webhook confirmation + emails, minimal dashboards for traveler/provider/admin, OpenAPI updated, tests added
