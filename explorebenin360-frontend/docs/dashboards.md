# Dashboards (Traveler, Provider, Admin)

This document summarizes the dashboard pages, routes, key components, and data contracts. Where backend endpoints are not yet available, stubbed services are used with a development feature flag.

Feature flag
- VITE_USE_STUBS=true will make typed services return mock data instead of calling the API.

Shared components
- BrandBanner — page headers with on-brand images
- EmptyState — consistent empty states with variants: default, search, favorites, bookings
- Card, Loader, EBImage, EBGallery — UI and media components
- SmallAreaChart — lightweight SVG area chart used in dashboard widgets

Typed services (src/lib/services)
- bookings.ts
  - travelerBookings(): Booking[]
  - providerBookingsService({ status?, from?, to? }): Booking[]
  - adminBookingsService({ status?, q? }): Booking[]
  - fetchBookingService(id): Booking
  - cancelBookingService(id)
  - adminUpdateBookingStatus(id, status)
- offerings.ts
  - providerOfferings(): Offering[]
  - createOffering(payload): Offering
  - updateOffering(id, payload): Offering
  - fetchOfferingById(id): Offering
- providers.ts
  - listProviders({ status? }): ProviderUser[]
  - approveProvider(id)
  - rejectProvider(id)
- favorites.ts
  - fetchFavorites(): { places: Place[], accommodations: Accommodation[], articles: Article[] }
- messages.ts
  - listThreads(): Thread[]
  - listMessages(threadId): Message[]
  - sendMessage(threadId, body): Message
- analytics.ts
  - computeProviderKPIs(bookings)
  - buildTimeseries(days, seed?) -> { date, value }[]

Types (src/types/business.ts)
- Booking, Offering, ProviderUser, Payout, Thread, Message (+ status enums)

Routes
Traveler (requires auth)
- /dashboard/reservations — list with filters (upcoming/past/all), receipt link when available
- /dashboard/reservations/:id — detail with cancel action (if allowed)
- /dashboard/favorites — saved places/stays/articles; EmptyState favorites
- /dashboard/messages — threads list, conversation view; stubbed service

Provider (requires auth)
- /provider — ProviderDashboard: KPIs (total, confirmed, gross, net, commission) and a chart with 7/30/90d filter
- /provider/reservations — list with status/date filters; EmptyState when none
- /provider/offers — list with status toggle; create button
- /provider/offers/new — create form (static-first, URL fields)
- /provider/offers/:id — edit form + simple gallery management
- /provider/calendar — availability viewer with month/week toggle; block-out dates (stub update)
- /provider/earnings — payouts table; Export CSV (client-side)

Admin (requires auth)
- /admin — AdminDashboard with KPIs, chart, recent activity
- /admin/reservations — oversight list; quick status update; filters and search
- /admin/providers — approval queue with approve/reject actions (PATCH placeholders to /api/v1/admin/providers/{id}/approve)
- /admin/moderation — list of reports/pending items with actions; EmptyState when empty

Data contracts
- Bookings
  - { id, offering: { id, title, slug? }, user?, start_date, end_date?, guests, status, currency, amount, commission_amount?, receipt_url? }
  - Status transitions for admin update: pending|authorized|confirmed|cancelled|refunded
- Offerings
  - { id, title, slug, description?, price, currency, capacity?, cover_image_url?, gallery?, status, availability_json? }
- Providers
  - { id, name, email, phone?, status: pending|approved|rejected, kyc_submitted?, kyc_verified? }
- Messages
  - Thread { id, subject, unread_count, last_message_preview, updated_at }
  - Message { id, thread_id, author { id, name }, body, created_at }

Backend integration
- Reuses axios client (src/lib/api.ts) with base URL VITE_API_BASE_URL or /api/v1
- Where endpoints exist, services call them; otherwise return stubs with TODO comments
- TODO endpoints (to confirm/implement):
  - GET /bookings (traveler)
  - GET /provider/offerings, POST /provider/offerings, PATCH /provider/offerings/:id
  - PATCH /admin/providers/:id/approve and /reject
  - Messages: GET /messages/threads, GET /messages/threads/:id, POST /messages/threads/:id

A11y & i18n
- All user-facing strings are i18n-ready (FR/EN)
- Buttons/links have focus-ring classes; images have alt text

Performance
- EBImage uses lazy-loading with LQIP and width/DPR srcsets; priority flag used in banners as needed
- Charts are lightweight SVG and do not block page load

Notes
- The UI is designed to be consistent with existing components and styles
- CSV export is client-side and works without backend support
