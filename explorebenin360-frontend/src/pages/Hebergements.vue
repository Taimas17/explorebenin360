<template>
  <div class="container-px mx-auto py-8 space-y-6">
    <BrandBanner :src="banner" alt="" :title="t('nav.hebergements')" class="mb-6" :priority="true" />

    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex-1 min-w-[240px]">
        <label class="block text-sm font-medium mb-1">Recherche</label>
        <input v-model="q" type="search" class="w-full rounded-lg border px-3 py-2" placeholder="Rechercher" />
      </div>
      <div class="min-w-[220px]">
        <MultiSelect v-model="types" :options="typeOptions" :label="t('filters.type') || 'Type'" />
      </div>
      <div class="min-w-[220px]">
        <label class="block text-sm font-medium mb-1">Ville</label>
        <input v-model.trim="city" class="w-full rounded-lg border px-3 py-2" placeholder="Cotonou" />
      </div>
      <div class="min-w-[240px]">
        <RangeSlider v-model="price" :label="t('filters.price') || 'Prix/nuit'" />
      </div>
      <div class="min-w-[160px]">
        <label class="block text-sm font-medium mb-1">Capacité</label>
        <input v-model.number="capacity" type="number" min="1" class="w-full rounded-lg border px-3 py-2" />
      </div>
      <div class="ml-auto">
        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" v-model="mapOn" /> Carte</label>
      </div>
    </div>

    <div v-if="mapOn"><MapShell :markers="markers" @marker-click="onMarkerClick" class="mt-2"/></div>

    <div class="mt-6">
      <div v-if="loading" class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
      <div v-else-if="error" class="text-red-600 text-sm">{{ error }}</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="h in items" :key="h.id" :data-id="'h-' + h.id" :class="highlighted === h.id ? 'ring-2 ring-[color:var(--color-secondary)]' : ''">
          <template #media>
            <EBImage :src="h.cover_image_url || hebergementThumb" :alt="buildAlt('hebergement', h.title, h.city)" :width="800" :height="600" aspect-ratio="4 / 3" class="w-full h-auto" sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw" />
            <div class="absolute top-2 right-2">
              <FavoriteToggle type="hebergement" :id="h.id" size="sm" :entity="{ id: h.id, title: h.title, slug: h.slug, cover_image_url: h.cover_image_url, city: h.city, price_per_night: h.price_per_night, currency: h.currency }" />
            </div>
          </template>
          <template #title>{{ h.title }}</template>
          {{ h.city }} · {{ h.price_per_night.toLocaleString() }} {{ h.currency }}
          <template #actions>
            <RouterLink :to="{ name: 'hebergement-detail', params: { slug: h.slug } }" class="inline-block">
              <Button variant="secondary" size="sm">Voir</Button>
            </RouterLink>
          </template>
        </Card>
      </div>

      <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-[color:var(--color-text-muted)]">{{ meta.total }} résultats</div>
        <div class="flex items-center gap-2">
          <button class="px-3 py-1 rounded border" :disabled="page<=1" @click="setPage(page-1)">Précédent</button>
          <span class="text-sm">{{ page }}</span>
          <button class="px-3 py-1 rounded border" :disabled="page>=maxPage" @click="setPage(page+1)">Suivant</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useHead } from '@vueuse/head'
import { setPageMeta } from '@/utils/meta'
import { useRoute, useRouter } from 'vue-router'
import { fetchAccommodations } from '@/lib/api'
import Card from '@/components/ui/Card.vue'
import Button from '@/components/ui/Button.vue'
import Loader from '@/components/ui/Loader.vue'
import MultiSelect from '@/components/filters/MultiSelect.vue'
import RangeSlider from '@/components/filters/RangeSlider.vue'
import MapShell from '@/components/maps/MapShell.vue'
import EBImage from '@/components/media/EBImage.vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'
import { buildAlt } from '@/utils/a11y'
import hebergementsBanner from '@/assets/brand/images/hebergements/banner-default.png'
import hebergementThumb from '@/assets/brand/images/thumbs/hebergement-thumb.png'

const { t } = useI18n()
setPageMeta({ title: 'Hébergements — ExploreBenin360', description: t('brand.baseline'), path: '/hebergements', image: '/og-image.png' })
const banner = hebergementsBanner

const route = useRoute(); const router = useRouter()
const q = ref(route.query.q?.toString() || '')
const types = ref(route.query.type ? route.query.type.toString().split(',') : [])
const city = ref(route.query.city?.toString() || '')
const price = ref([route.query.min_price ? Number(route.query.min_price) : null, route.query.max_price ? Number(route.query.max_price) : null])
const capacity = ref(route.query.capacity ? Number(route.query.capacity) : 1)
const page = ref(route.query.page ? Number(route.query.page) : 1)
const per_page = ref(12)
const mapOn = ref(route.query.map === '1')

const typeOptions = [
  { value: 'hotel', label: 'Hôtel' },
  { value: 'guesthouse', label: 'Maison d’hôtes' },
  { value: 'ecolodge', label: 'Écolodge' },
  { value: 'bnb', label: 'BnB' },
  { value: 'other', label: 'Autre' },
]

const items = ref([])
const meta = reactive({ total: 0, current_page: 1, per_page: 12 })
const loading = ref(false)
const error = ref('')
const highlighted = ref(null)

const updateQuery = () => {
  const query = {
    q: q.value || undefined,
    type: types.value.length ? types.value.join(',') : undefined,
    city: city.value || undefined,
    min_price: price.value?.[0] || undefined,
    max_price: price.value?.[1] || undefined,
    capacity: capacity.value && capacity.value !== 1 ? capacity.value : undefined,
    page: page.value !== 1 ? page.value : undefined,
    map: mapOn.value ? '1' : undefined,
  }
  router.replace({ query })
}

let tHandle
watch([q, types, city, price, capacity], () => { clearTimeout(tHandle); tHandle = setTimeout(() => { page.value = 1; updateQuery(); load() }, 350) })
watch(page, () => { updateQuery(); load() })
watch(mapOn, () => updateQuery())

const load = async () => {
  loading.value = true; error.value = ''
  try {
    const { data, meta: m } = await fetchAccommodations({ q: q.value, type: types.value[0], city: city.value || undefined, min_price: price.value?.[0] || undefined, max_price: price.value?.[1] || undefined, capacity: capacity.value || undefined, page: page.value, per_page: per_page.value })
    items.value = data; Object.assign(meta, m)
  } catch (e) { error.value = 'Erreur de chargement' } finally { loading.value = false }
}

const markers = computed(() => items.value.map(h => ({ lat: h.lat, lng: h.lng, title: h.title, id: h.id })))
const onMarkerClick = (m) => { highlighted.value = m.id; const card = document.querySelector(`[data-id="h-${m.id}"]`); card?.scrollIntoView({ behavior: 'smooth', block: 'center' }) }
const maxPage = computed(() => Math.max(1, Math.ceil(meta.total / meta.per_page)))
const setPage = (p) => { if (p>=1 && p<=maxPage.value) page.value = p }

onMounted(load)
</script>
