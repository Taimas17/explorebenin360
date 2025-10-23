# ExploreBenin360 — Frontend

Vue 3 + Vite app with Tailwind CSS v4, Vue Router, and Vue I18n.

## Design tokens

Tokens live in `src/styles/tokens.css` using Tailwind v4 `@theme` variables:

- Colors: `--color-primary` `--color-secondary` `--color-accent`, plus light/dark text and backgrounds
- Radii, shadows, transitions
- Fonts: `--font-display` (Poppins) and `--font-sans` (Inter)

Global styles are in `src/styles/globals.css` and include Tailwind imports, base typography, link hovers, focus rings, and dark mode support. Add your components/pages and rely on these tokens for consistent styling.

## Branding

Logo assets live in `src/assets/brand/`. Variants include full-color, icon-only, and monochrome. Favicon and OG image are in `public/`.

## Internationalization

- Locale files: `src/i18n/fr.json`, `src/i18n/en.json`
- Initial locale is detected and stored in `localStorage (eb360:locale)`

## Maps

`src/components/maps/MapShell.vue` reads `VITE_GOOGLE_MAPS_API_KEY`. Copy `.env.example` to `.env` and set your key if needed.

## Media system

Reusable media components with responsive images, galleries, videos, and 360° panoramas are provided in `src/components/media/`:

- EBImage — responsive image with srcset, sizes, lazy-loading and decoding="async"
- EBGallery — grid + accessible lightbox with captions and keyboard nav
- EB360Viewer — wrapper around Pannellum loaded dynamically from CDN
- EBVideo — HTML5 video with poster and controls

Env variables (copy from `.env.example`):
```
VITE_MEDIA_PROVIDER=cloudinary|s3
VITE_CLOUDINARY_CLOUD_NAME=
VITE_CLOUDINARY_BASE_URL=
VITE_MEDIA_MAX_WIDTH=1600
```
Pages (Home, Destinations, Hébergements, Guides, Blog) include sample usage.

## Development

```sh
npm install
npm run dev
```

## Build

```sh
npm run build
```

## Environment Setup (staging)

- Copy `.env.example` to `.env.local` for local development:
  - Windows/Unix: `cp .env.example .env.local`
- Fill the following variables:
  - Required: `VITE_MEDIA_PROVIDER`, `VITE_CLOUDINARY_CLOUD_NAME`
  - Optional: `VITE_CLOUDINARY_BASE_URL` (defaults to `https://res.cloudinary.com/<cloud_name>/`), `VITE_MEDIA_MAX_WIDTH` (default `1600`), `VITE_GOOGLE_MAPS_API_KEY`
- Cloudinary values are available in Dashboard → Settings → Account. Maps key from Google Cloud Console.
- Security: Variables prefixed with `VITE_` are exposed to the browser. Never include API secrets or a full `CLOUDINARY_URL` in the frontend.
