Brand assets pack

Overview
This document lists the branded UI images bundled locally with the frontend, with usage guidelines and light/dark considerations. All assets live under src/assets/brand and are imported via Vite for bundling reliability.

Structure
- images/
  - home/
    - hero-1.png
    - hero-2.png
    - hero-3.png
  - destinations/
    - banner-default.png
  - hebergements/
    - banner-default.png
  - blog/
    - cover-default.png
  - dashboard/
    - traveler/
      - header.png
    - provider/
      - header.png
    - admin/
      - header.png
  - placeholders/
    - empty-default.png
    - empty-search.png
    - empty-favorites.png
    - empty-bookings.png
    - error-default.png
  - thumbs/
    - destination-thumb.png
    - hebergement-thumb.png
    - guide-thumb.png
    - article-thumb.png
    - event-thumb.png

Usage guidelines
- Banners and headers
  - Component: BrandBanner
  - Typical rendered size: 16:9, responsive height 36–44vh
  - Suggested source dimensions: ≥1600x900 (landscape)
  - Overlay: BrandBanner applies a dark gradient to ensure text contrast. Avoid embedding text inside images; use slots.
- Cards and thumbnails
  - Component: EBImage
  - Aspect ratio: 4:3 for grid cards
  - Sizes: width/height set in call sites (e.g., 800x600 or 1200x900) with aspect-ratio to prevent CLS
  - Fallbacks: When API provides no media, use the corresponding thumb from images/thumbs
- Empty states
  - Component: EmptyState
  - Variants: default, search, favorites, bookings
  - Each variant maps to an illustration under images/placeholders (configurable via imageSrc override)

Light/dark considerations
- Images are shared between light and dark. BrandBanner overlays increase contrast automatically; ensure readable text in both modes.
- When adding new banners, prefer images with clear subjects and sufficient negative space for overlays.

Imports
- Always import assets via Vite to ensure bundling:
  - import destinationsBanner from '@/assets/brand/images/destinations/banner-default.png'
  - import eventThumb from '@/assets/brand/images/thumbs/event-thumb.png'
- Avoid raw '/src/...' string paths at runtime.

Updating assets
- Replace files in src/assets/brand with new versions keeping the same filenames to avoid code changes.
- If you add new filenames, update the corresponding imports and constants in pages/components.

Provider readiness
- EBImage remains provider-ready. Local files are used now; if Cloudinary is enabled later (VITE_MEDIA_PROVIDER=cloudinary), EBImage will automatically serve optimized URLs.

Accessibility
- Always pass a meaningful alt for content images (what/where). For decorative images (e.g., some empty states), use empty alt.

Performance
- Pages set explicit width/height or aspect-ratio on EBImage to prevent layout shifts.
- Cards use 4:3 aspect and appropriate sizes attributes to help the browser pick the right source.
