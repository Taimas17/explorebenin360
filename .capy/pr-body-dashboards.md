## Summary
- Deliver complete Traveler, Provider, and Admin dashboards aligned with the product spec, reusing existing components and adding typed services with a stub flag for missing endpoints.
- Keep scope frontend-focused while wiring to real backend endpoints where available; cleanly stub the rest and document TODOs.

## Changes
- Traveler dashboard
  - Reservations list with upcoming/past/all filters, status and receipt links, and details page with cancel action when allowed.
  - Favorites page rendering saved destinations/stays/articles (stub data), consistent EmptyState and banners.
  - Messages page with threads list, unread badges, conversation view, and sending (stub services for now).
- Provider dashboard
  - Home KPIs (total, confirmed, gross, commission, net) and a lightweight chart with 7/30/90d timespan.
  - Reservations with status/date filters and EmptyState.
  - Offers management: list with status toggle; create/edit forms; simple gallery (URL-first). Stubs with clear TODOs.
  - Calendar/availability viewer with month/week toggle and block-out dates (stub update).
  - Earnings table with client-side CSV export.
- Admin dashboard
  - Overview KPIs, chart, recent activity.
  - Reservations oversight with backend-supported filters (status, from, to), plus client-side quick search and inline status update.
  - Providers approval list with KYC flags (stubbed approve/reject endpoints, TODOs noted).
  - Content moderation (stub) with EmptyState.
- Routing
  - Added routes for all traveler/provider/admin pages.
- Integration & services
  - Reused axios client (src/lib/api.ts). Created typed services under src/lib/services for bookings, offerings, providers, favorites, messages, analytics.
  - Services default to real endpoints; set VITE_USE_STUBS=true to enable mock data for missing endpoints.
- i18n & a11y
  - All new strings localized (FR/EN). Focus rings and alt text throughout.
- Docs
  - docs/dashboards.md summarizing pages, routes, components, and contracts with TODOs.

## Why
- Provide complete, navigable dashboards per persona to unblock product review and staged backend integration, while maintaining performance, i18n, and consistent UX.

## Endpoints
- Real endpoints used now:
  - GET /api/v1/bookings, GET /api/v1/bookings/:id, POST /api/v1/bookings/:id/cancel
  - GET /api/v1/provider/bookings
  - GET /api/v1/admin/bookings (status, from, to filters), PATCH /api/v1/admin/bookings/:id (status)
- Missing endpoints left stubbed with TODOs:
  - Provider offerings CRUD, Admin providers approval, Messages, Favorites

## Impact
- Charts are lightweight and non-blocking; CSV export works client-side.
- All dashboards fully navigable, banners consistent, and EmptyStates aligned with brand.

## Notes / TODOs
- Implement provider offering CRUD endpoints and admin provider approval endpoints to replace stubs.
- Confirm messages and favorites backend contracts, then wire services.
