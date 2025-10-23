<template>
  <div class="container-px mx-auto py-8 space-y-6">
    <BrandBanner :src="banner" alt="Bannière Explorer" :title="t('nav.explorer')" class="mb-2" />

    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex-1 min-w-[240px]">
        <label class="block text-sm font-medium mb-1">Recherche</label>
        <input v-model="q" type="search" class="w-full rounded-lg border px-3 py-2" :placeholder="t('search.placeholder') || 'Rechercher'" />
      </div>
      <div class="min-w-[240px]">
        <label class="block text-sm font-medium mb-1">Types</label>
        <div class="flex flex-wrap gap-2">
          <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" value="destination" v-model="types"/> Destination</label>
          <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" value="hebergement" v-model="types"/> Hébergement</label>
          <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" value="guide" v-model="types"/> Guide</label>
        </div>
      </div>
      <div class="ml-auto flex items-center gap-2 text-sm text-[color:var(--color-text-muted)]">
        <span>Lat: {{ center?.lat?.toFixed(4) || '-' }}</span>
        <span>Lng: {{ center?.lng?.toFixed(4) || '-' }}</span>
        <span>Zoom: {{ zoom }}</span>
      </div>
    </div>

    <div v-if="markers.length === 0" class="grid place-items-center py-12">
      <EmptyState variant="search" title="Aucun résultat" description="Ajustez vos filtres ou élargissez la zone.">
        <Button variant="primary" @click="clearFilters">Effacer les filtres</Button>
      </EmptyState>
    </div>
    <div v-else>
      <MapShell
        provider="leaflet"
        :markers="markers"
        :center="center"
        :zoom="zoom"
        :bbox="bbox"
        :fitOnMarkersChange="!bbox"
        cluster
        @marker-click="onMarkerClick"
        @bounds-change="onBoundsChange"
      />
    </div>
  </div>
</template>
<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { fetchPlaces, fetchAccommodations, fetchGuides } from '@/lib/api'
import MapShell from '@/components/maps/MapShell.vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import Button from '@/components/ui/Button.vue'

const { t } = useI18n()
const route = useRoute(); const router = useRouter()
const banner = '/src/assets/brand/images/destinations/banner-default.png'

const q = ref(route.query.q?.toString() || '')
const types = ref(route.query.type ? route.query.type.toString().split(',') : ['destination','hebergement'])
const zoom = ref(route.query.zoom ? Number(route.query.zoom) : 7)
const center = ref(route.query.lat && route.query.lng ? { lat: Number(route.query.lat), lng: Number(route.query.lng) } : null)
const bbox = ref(route.query.bbox ? route.query.bbox.toString().split(',').map(Number) : null)

const places = ref([])
const accommodations = ref([])
const guides = ref([])
const loading = ref(false)

const updateQuery = (extra = {}) => {
  const query = {
    q: q.value || undefined,
    type: types.value.length && types.value.length < 3 ? types.value.join(',') : undefined,
    zoom: zoom.value || undefined,
    lat: center.value?.lat || undefined,
    lng: center.value?.lng || undefined,
    bbox: bbox.value ? bbox.value.join(',') : undefined,
    ...extra,
  }
  router.replace({ query })
}

let tHandle
watch([q, types], () => { clearTimeout(tHandle); tHandle = setTimeout(() => { load() ; updateQuery() }, 350) })

const onBoundsChange = (p) => {
  zoom.value = p.zoom
  center.value = p.center
  bbox.value = p.bbox
  updateQuery()
  load()
}

const toHref = (type, slug) => {
  if (type==='destination') return { name: 'destination-detail', params: { slug } }
  if (type==='hebergement') return { name: 'hebergement-detail', params: { slug } }
  if (type==='guide') return { name: 'guides' }
  return { name: 'home' }
}

const markers = computed(() => {
  const out = []
  if (types.value.includes('destination')) {
    out.push(...places.value.map(p => ({ id: p.id, lat: p.lat, lng: p.lng, title: p.title, type: 'destination', href: router.resolve(toHref('destination', p.slug)).href })))
  }
  if (types.value.includes('hebergement')) {
    out.push(...accommodations.value.map(a => ({ id: a.id, lat: a.lat, lng: a.lng, title: a.title, type: 'hebergement', href: router.resolve(toHref('hebergement', a.slug)).href })))
  }
  if (types.value.includes('guide')) {
    out.push(...guides.value.filter(g => typeof g.lat==='number' && typeof g.lng==='number').map(g => ({ id: g.id, lat: g.lat, lng: g.lng, title: g.name, type: 'guide', href: router.resolve({ name: 'guides' }).href })))
  }
  return out
})

const onMarkerClick = (id) => {
  // For now, just navigate if we can find a matching marker
  const m = markers.value.find(m => m.id === id)
  if (m?.href) window.location.href = m.href
}

const load = async () => {
  loading.value = true
  try {
    const [p, a, g] = await Promise.all([
      fetchPlaces({ q: q.value, per_page: 100, bounds: bbox.value ? bbox.value.join(',') : undefined }),
      fetchAccommodations({ q: q.value, per_page: 100 }),
      fetchGuides({ q: q.value, per_page: 100 }),
    ])
    places.value = p.data || []
    accommodations.value = a.data || []
    guides.value = g.data || []
  } catch (e) {
    // noop
  } finally { loading.value = false }
}

const clearFilters = () => {
  q.value = ''
  types.value = ['destination','hebergement']
  zoom.value = 7
  center.value = null
  bbox.value = null
  updateQuery({ q: undefined, type: undefined, zoom: undefined, lat: undefined, lng: undefined, bbox: undefined })
  load()
}

onMounted(() => { load() })
</script>
