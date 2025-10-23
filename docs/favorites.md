# Favorites — ExploreBenin360

Objective
Implement a cross-entity Favorites system covering Destinations, Hébergements (Stays), Articles, and Guides. Users can toggle favorites on list cards and detail pages, with a dedicated Favorites page to review and manage them.

Data model
- Types: `destination | hebergement | article | guide`
- Store state: `items: Record<type, Set<id>>`
- Internal cache: `entities: Record<type, Record<id, MinimalEntity>>` used to render the Favorites page without extra API lookups.
- LocalStorage key: `eb360:favorites`

Persistence strategy
- Optimistic toggle: UI updates immediately. Persistence is done in the background.
- If the user is authenticated and Favorites endpoints exist on the API, favorites are persisted server-side with:
  - `GET /favorites` → initial load and login merge
  - `POST /favorites { type, id }` → add
  - `POST /favorites/remove { type, id }` → remove
- Graceful fallback: if these endpoints return 404, the store switches to local-only mode and persists to LocalStorage. No UX blocking.
- Merge on login: on successful login/register, the store fetches server favorites (if supported) and merges them with local favorites. Local-only items are then pushed to the server. Logout keeps local favorites intact.

UI components
- `FavoriteToggle.vue`
  - Props: `type`, `id`, `size = 'md'`, optional `entity` (for caching/minimal rendering on the Favorites page)
  - Accessibility: `role="switch"`, `aria-checked`, dynamic `aria-label` (i18n)
  - Emits: `change(isFav)`
- Integration: Toggle added to the top-right of cards on list pages and to the banner of detail pages via `BrandBanner` overlay slot.

Pages & routing
- Favorites page: `src/pages/dashboard/Favorites.vue`
  - Sections by type, using existing `Card` and `EBImage`/`AvatarFallback`
  - Empty state: `EmptyState variant="favorites"` with CTA to explore
- Routing: `/dashboard/favorites`, linked from Navbar (heart icon) and Profile

i18n & a11y
- FR/EN keys for navbar label, page titles, empty state text and aria labels.
- Toggle is keyboard accessible and uses visible focus ring.

Future extensions
- Server-side sync and conflict resolution strategies
- Shareable favorites lists (public URLs)
- Bulk actions (clear all, export)
- Notification hooks when a favorited item changes (price drop, new pictures)
