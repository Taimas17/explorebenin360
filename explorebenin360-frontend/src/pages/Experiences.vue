<template>
  <main class="container-px mx-auto py-10 space-y-6">
    <div class="flex justify-between items-center mb-4 gap-3 flex-wrap">
      <h1 class="text-3xl font-bold">{{ t('nav.experiences') }}</h1>
      <div class="flex flex-wrap gap-3 items-end">
        <div class="min-w-[220px]">
          <label class="block text-sm font-medium mb-1">{{ t('search.placeholder') || 'Recherche' }}</label>
          <input
            v-model="q"
            type="search"
            class="w-full rounded-lg border px-3 py-2"
            :placeholder="t('search.placeholder') || 'Rechercher'"
          />
        </div>
        <div class="min-w-[220px]">
          <MultiSelect
            v-model="typeFilter"
            :options="categoryOptions"
            :label="t('experiences.filters.category')"
          />
        </div>
        <div class="min-w-[220px]">
          <label class="block text-sm font-medium mb-1">{{ t('experiences.filters.location') }}</label>
          <input
            v-model.trim="city"
            class="w-full rounded-lg border px-3 py-2"
            placeholder="Cotonou"
          />
        </div>
        <div class="min-w-[240px]">
          <RangeSlider
            v-model="price"
            :label="t('experiences.filters.price_range')"
          />
        </div>
        <div class="min-w-[200px]">
          <label class="block text-sm font-medium mb-1">Sort</label>
          <select v-model="sort" class="w-full rounded-lg border px-3 py-2 text-sm">
            <option value="popular">{{ t('experiences.sort.popular') }}</option>
            <option value="price_asc">{{ t('experiences.sort.price_asc') }}</option>
            <option value="price_desc">{{ t('experiences.sort.price_desc') }}</option>
            <option value="newest">{{ t('experiences.sort.newest') }}</option>
          </select>
        </div>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <Loader />
    </div>

    <div v-else-if="error" class="max-w-xl mx-auto py-10">
      <Alert variant="error">{{ error }}</Alert>
    </div>

    <div v-else-if="items.length === 0" class="py-10">
      <EmptyState :title="t('experiences.empty')" :description="t('experiences.empty_desc')" />
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <Card v-for="offering in items" :key="offering.id">
        <template #media>
          <EBImage
            :src="offering.cover_image_url || placeholderOffering"
            :alt="offering.title"
            aspect-ratio="4 / 3"
            class="w-full h-auto"
          />
          <div class="absolute top-2 right-2">
            <FavoriteToggle
              type="offering"
              size="sm"
              :id="offering.id"
              :entity="{
                id: offering.id,
                title: offering.title,
                slug: offering.slug,
                cover_image_url: offering.cover_image_url,
                price: offering.price,
                currency: offering.currency,
                city: offering.place?.city || ''
              }"
            />
          </div>
        </template>
        <template #title>{{ offering.title }}</template>
        <p class="text-sm text-gray-600">
          {{ offering.place?.city || '' }}
        </p>
        <p class="font-semibold mt-2">
          {{ formatPrice(offering.price, offering.currency) }}
        </p>
        <template #actions>
          <RouterLink
            :to="{ name: 'offering-detail', params: { slug: offering.slug } }"
            class="inline-block"
          >
            <Button variant="secondary" size="sm">{{ t('common.details') }}</Button>
          </RouterLink>
        </template>
      </Card>
    </div>

    <div
      v-if="meta.total > 0 && maxPage > 1"
      class="mt-8 flex items-center justify-between"
    >
      <div class="text-sm text-[color:var(--color-text-muted)]">
        {{ meta.total }} résultats
      </div>
      <div class="flex items-center gap-2">
        <button
          class="px-3 py-1 rounded border text-sm disabled:opacity-50"
          :disabled="page <= 1"
          @click="setPage(page - 1)"
        >
          Précédent
        </button>
        <span class="text-sm">{{ page }}</span>
        <button
          class="px-3 py-1 rounded border text-sm disabled:opacity-50"
          :disabled="page >= maxPage"
          @click="setPage(page + 1)"
        >
          Suivant
        </button>
      </div>
    </div>
  </main>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { setPageMeta } from '@/utils/meta'
import { fetchOfferings } from '@/lib/api'
import Card from '@/components/ui/Card.vue'
import Button from '@/components/ui/Button.vue'
import Loader from '@/components/ui/Loader.vue'
import Alert from '@/components/ui/Alert.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import EBImage from '@/components/media/EBImage.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'
import MultiSelect from '@/components/filters/MultiSelect.vue'
import RangeSlider from '@/components/filters/RangeSlider.vue'

const { t } = useI18n()
setPageMeta({
  title: t('meta.experiences_title'),
  description: t('meta.experiences_desc'),
  path: '/experiences',
  image: '/og-image.png',
})

const route = useRoute()
const router = useRouter()

const q = ref(route.query.q?.toString() || '')
const typeFilter = ref(route.query.type?.toString() || 'experience')
const city = ref(route.query.city?.toString() || '')
const price = ref<[number | null, number | null]>([
  route.query.min_price ? Number(route.query.min_price) : null,
  route.query.max_price ? Number(route.query.max_price) : null,
])
const sort = ref((route.query.sort as string) || 'popular')
const page = ref(route.query.page ? Number(route.query.page) : 1)
const per_page = ref(12)

const items = ref<any[]>([])
const meta = reactive({ total: 0, current_page: 1, per_page: 12 })
const loading = ref(false)
const error = ref('')

const placeholderOffering = '/placeholder/offering.png'

const categoryOptions = [
  { value: '', label: t('filters.all') },
  { value: 'experience', label: t('provider.type_experience') },
  { value: 'accommodation', label: t('provider.type_accommodation') },
  { value: 'guide_service', label: t('provider.type_guide_service') },
]

const maxPage = computed(() => {
  if (!meta.per_page) return 1
  return Math.max(1, Math.ceil(meta.total / meta.per_page))
})

const updateQuery = () => {
  const query: Record<string, any> = {
    q: q.value || undefined,
    type: typeFilter.value || undefined,
    city: city.value || undefined,
    min_price: price.value?.[0] || undefined,
    max_price: price.value?.[1] || undefined,
    sort: sort.value !== 'popular' ? sort.value : undefined,
    page: page.value !== 1 ? page.value : undefined,
  }
  router.replace({ query })
}

let tHandle: any
watch([q, typeFilter, city, price, sort], () => {
  clearTimeout(tHandle)
  tHandle = setTimeout(() => {
    page.value = 1
    updateQuery()
    load()
  }, 350)
})

watch(page, () => {
  updateQuery()
  load()
})

const load = async () => {
  loading.value = true
  error.value = ''
  try {
    const { data, meta: m }: any = await fetchOfferings({
      q: q.value || undefined,
      type: typeFilter.value || undefined,
      city: city.value || undefined,
      min_price: price.value?.[0] || undefined,
      max_price: price.value?.[1] || undefined,
      sort: sort.value || undefined,
      page: page.value,
      per_page: per_page.value,
    })
    items.value = data
    Object.assign(meta, m)
  } catch (e) {
    error.value = 'Erreur de chargement'
  } finally {
    loading.value = false
  }
}

const setPage = (p: number) => {
  if (p >= 1 && p <= maxPage.value) page.value = p
}

const formatPrice = (price: any, currency: string) => {
  const value = Number(price || 0)
  return `${value.toLocaleString()} ${currency}`
}

onMounted(load)
</script>
