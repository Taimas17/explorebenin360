# Explorer Map — Provider-agnostic architecture

This document describes how the Explorer map is built without any API key using Leaflet + OpenStreetMap tiles, how URL synchronization works, and how to migrate later to Mapbox or Google while preserving a stable interface.

## Architecture overview

- MapShell.vue is the provider-agnostic façade. It exposes stable props/events and internally renders a provider implementation based on `provider`.
- LeafletMap.vue is the default provider implementation. It uses:
  - Leaflet for the base map, OSM tiles (no key required)
  - leaflet.markercluster for clustering
  - Sensible defaults: fit-to-bounds, maxZoom limit, accessible popups
- Explorer.vue wires data (markers) from API results, synchronizes map state with the URL, and handles the zero-result EmptyState.

```
Explorer.vue
  └─ MapShell.vue (provider switch)
       └─ LeafletMap.vue (provider: 'leaflet')
```

## Stable interface (props/events)

MapShell forwards these props to the active provider:
- markers: Array<{ id, lat, lng, title, subtitle?, type?, href? }>
- zoom?: number
- center?: { lat, lng }
- bbox?: [west, south, east, north] | null
- cluster?: boolean (default true)
- fitOnMarkersChange?: boolean (default true)

Events emitted upward:
- marker-click(id)
- bounds-change({ bbox, center, zoom })

These are intentionally generic so you can swap providers without changing page code.

## URL conventions

Query parameters used by Explorer:
- q: free-text search
- type: comma-separated subset of [destination, hebergement, guide]
- lat, lng: current map center
- zoom: current zoom
- bbox: current bounds as `w,s,e,n`

Hydration rules:
- On page load, if `bbox` is present, the map fits to that bbox.
- Else if `lat/lng/zoom` are present, they define initial view.
- Else the map will fit to current markers.

Change propagation:
- Map view changes (panning/zooming) emit bounds-change, which updates the URL without reloading the page.
- Filter changes update the URL and reload data.

## Accessibility and performance

- Popups render lightweight HTML strings (title + optional link). Close button is focusable; focus-visible styles are provided.
- Marker layers are updated without re-creating the map instance to keep interactions smooth.
- Cluster click zooms to cluster bounds; max zoom capped to avoid over-zoom.
- Map container uses responsive height and expands to 100% of available space.

## Switching providers later

To add Mapbox or Google providers, create new components next to `LeafletMap.vue` and extend the switch in `MapShell.vue`:

- Mapbox:
  - Install `mapbox-gl` and a Vue wrapper (optional) or use the JS SDK directly.
  - Add an environment variable for the access token, e.g. `VITE_MAPBOX_TOKEN`.
  - Implement the same props/events contract. Use Mapbox clustering or Supercluster.
  - Replace OSM tiles with a Mapbox style URL.

- Google Maps:
  - Install the Google Maps JS API loader.
  - Add `VITE_GOOGLE_MAPS_API_KEY` and load the script dynamically.
  - Implement the same props/events: markers, clustering (MarkerClustererPlus), bounds-change.

Cost control & best practices:
- Only load the provider SDK when `provider` is selected.
- Debounce map-move events before writing to URL if needed.
- Keep marker popups simple; lazy-load heavy content on demand.

## Developer notes

- Leaflet CSS and markercluster CSS are imported inside `LeafletMap.vue` to scope styles to the component.
- Default marker icons are patched for Vite by merging URL options into `L.Icon.Default`.
- Clustering is disabled above `maxZoom - 2` for easier inspection at street level.
