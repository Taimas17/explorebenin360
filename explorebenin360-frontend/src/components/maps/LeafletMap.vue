<template>
  <div ref="mapEl" class="relative w-full h-full min-h-[320px]" role="region" aria-label="Carte interactive"></div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, watch, toRaw } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet.markercluster/dist/MarkerCluster.css'
import 'leaflet.markercluster/dist/MarkerCluster.Default.css'
import 'leaflet.markercluster'

// Fix default marker icons with Vite
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png'
import iconUrl from 'leaflet/dist/images/marker-icon.png'
import shadowUrl from 'leaflet/dist/images/marker-shadow.png'
L.Icon.Default.mergeOptions({ iconRetinaUrl, iconUrl, shadowUrl })

const props = defineProps({
  markers: { type: Array, default: () => [] },
  zoom: { type: Number, default: 6 },
  center: { type: Object, default: null }, // { lat, lng }
  bbox: { type: Array, default: null },   // [w,s,e,n]
  cluster: { type: Boolean, default: true },
  fitOnMarkersChange: { type: Boolean, default: true },
  maxZoom: { type: Number, default: 16 },
})

const emit = defineEmits(['marker-click','bounds-change'])

const mapEl = ref(null)
let map
let tile
let clusterGroup
let plainLayer

const toBounds = (bbox) => L.latLngBounds([[bbox[1], bbox[0]], [bbox[3], bbox[2]]])

const applyBoundsFromProps = () => {
  if (!map) return
  if (props.bbox && props.bbox.length === 4) {
    const b = toBounds(props.bbox)
    map.fitBounds(b, { padding: [20, 20], maxZoom: props.maxZoom })
  } else if (props.center && typeof props.center.lat === 'number' && typeof props.center.lng === 'number') {
    map.setView([props.center.lat, props.center.lng], props.zoom || 6)
  }
}

const emitBounds = () => {
  if (!map) return
  const b = map.getBounds()
  const c = map.getCenter()
  emit('bounds-change', {
    bbox: [b.getWest(), b.getSouth(), b.getEast(), b.getNorth()],
    center: { lat: c.lat, lng: c.lng },
    zoom: map.getZoom(),
  })
}

const rebuildMarkers = () => {
  if (!map) return
  if (clusterGroup) clusterGroup.clearLayers()
  if (plainLayer) plainLayer.clearLayers()

  const addMarker = (m) => {
    const marker = L.marker([m.lat, m.lng])
    const title = m.title || ''
    const href = m.href || null
    const html = href ? `<strong>${title}</strong><br/><a href="${href}">Voir</a>` : `<strong>${title}</strong>`
    marker.bindPopup(html, { autoPanPadding: [24,24] })
    marker.on('click', () => emit('marker-click', m.id))
    return marker
  }

  if (props.cluster) {
    if (!clusterGroup) {
      clusterGroup = L.markerClusterGroup({
        chunkedLoading: true,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        disableClusteringAtZoom: props.maxZoom - 2,
      })
      clusterGroup.on('clusterclick', (e) => {
        e.layer.zoomToBounds({ padding: [20, 20] })
      })
      map.addLayer(clusterGroup)
    }
    props.markers?.forEach((m) => {
      if (typeof m.lat === 'number' && typeof m.lng === 'number') clusterGroup.addLayer(addMarker(m))
    })
  } else {
    if (!plainLayer) {
      plainLayer = L.layerGroup()
      map.addLayer(plainLayer)
    }
    props.markers?.forEach((m) => {
      if (typeof m.lat === 'number' && typeof m.lng === 'number') plainLayer.addLayer(addMarker(m))
    })
  }

  if ((props.fitOnMarkersChange !== false) && props.markers && props.markers.length) {
    const latlngs = props.markers.filter(m => typeof m.lat==='number' && typeof m.lng==='number').map(m => [m.lat, m.lng])
    if (latlngs.length) {
      const bounds = L.latLngBounds(latlngs)
      map.fitBounds(bounds, { padding: [20,20], maxZoom: props.maxZoom })
    }
  }
}

onMounted(() => {
  map = L.map(toRaw(mapEl.value), { zoomControl: true, attributionControl: true })
  tile = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  })
  tile.addTo(map)

  map.on('moveend zoomend', emitBounds)

  applyBoundsFromProps()
  rebuildMarkers()
})

onBeforeUnmount(() => {
  if (map) {
    map.off()
    map.remove()
  }
})

watch(() => [props.center, props.zoom, props.bbox], () => {
  // If external state drives the map (URL sync), apply it
  applyBoundsFromProps()
})

watch(() => [props.markers, props.cluster], () => {
  rebuildMarkers()
}, { deep: true })
</script>

<style scoped>
:global(.leaflet-container) { outline: none; }
:global(.leaflet-popup-close-button) { outline: 2px solid transparent; }
:global(.leaflet-popup-close-button:focus-visible) { outline-color: var(--color-secondary); }
</style>
