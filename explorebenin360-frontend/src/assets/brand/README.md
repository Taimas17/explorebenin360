Brand imagery system (SCO-001/SCO-002)

Structure
- images/
  - home/
  - destinations/
  - hebergements/
  - guides/
  - blog/
  - dashboard/
    - traveler/
    - provider/
    - admin/
  - placeholders/

Guidelines
- Art-direction per breakpoint:
  - Mobile: tighter crop on subject; avoid small details. Prefer centered subjects; keep text-safe area top-left.
  - Tablet/Desktop: wider crop; allow contextual scenery. Ensure important subjects are within center 60% horizontally.
- Overlays and contrast:
  - Always apply a dark gradient overlay on banners to achieve WCAG AA for white text (target contrast ≥ 4.5:1).
  - Use brand overlay: orange (#FF6B35) and teal (#00796B) with 60–75% opacity at the lower edge fading to 0%.
- Dark mode:
  - Reuse same images; increase overlay opacity by ~10–20% in dark mode.
  - Optionally provide dedicated dark images via props; otherwise, overlays ensure legibility.
- Alt text:
  - Meaningful images require concise, descriptive alt (what/where/when). Decorative images use empty alt (alt="").
- Performance:
  - Use EBImage with responsive srcset and sizes: widths 400/800/1200/1600; DPR-aware on Cloudinary with f=auto,q=auto.
  - Declare width/height or aspect-ratio to avoid CLS (< 0.02).
  - Preload the largest hero image of the first viewport only; keep others lazy.
- Formats:
  - Prefer AVIF/WebP (f=auto) with JPEG fallback handled by Cloudinary. Local assets should be PNG/JPEG; EBImage optimizes when served via Cloudinary.
- Credits for temporary imagery:
  - Temporary assets may come from Unsplash/Pexels or Cloudinary sample images. Replace easily by editing the constants paths in pages/components.

Usage
- Use BrandBanner for page headers with image + overlay + slots for title/subtitle/CTA.
- Use EBImage for cards and inline images; pass aspectRatio to reserve layout space.
- Use EBGallery for hero sliders and galleries; thumbnails are keyboard accessible; lightbox supports arrows and Escape.
- Use EmptyState in dashboards and listings when there is no data (variants: default, search, favorites, bookings).

Cloudinary
- If VITE_MEDIA_PROVIDER=cloudinary and VITE_CLOUDINARY_CLOUD_NAME is set, EBImage will generate f=auto,q=auto,dpr srcsets automatically.
- Helper widths: 400, 800, 1200, 1600.
