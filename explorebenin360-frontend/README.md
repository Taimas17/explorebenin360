# explorebenin360-frontend

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VS Code](https://code.visualstudio.com/) + [Vue (Official)](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Recommended Browser Setup

- Chromium-based browsers (Chrome, Edge, Brave, etc.):
  - [Vue.js devtools](https://chromewebstore.google.com/detail/vuejs-devtools/nhdogjmejiglipccpnnnanhbledajbpd) 
  - [Turn on Custom Object Formatter in Chrome DevTools](http://bit.ly/object-formatters)
- Firefox:
  - [Vue.js devtools](https://addons.mozilla.org/en-US/firefox/addon/vue-js-devtools/)
  - [Turn on Custom Object Formatter in Firefox DevTools](https://fxdx.dev/firefox-devtools-custom-object-formatters/)

## Customize configuration

See [Vite Configuration Reference](https://vite.dev/config/).

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Compile and Minify for Production

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
