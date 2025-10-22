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

## Development

```sh
npm install
npm run dev
```

## Build

```sh
npm run build
```
