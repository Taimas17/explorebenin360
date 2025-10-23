<template>
  <div class="container-px mx-auto py-8 space-y-6">
    <h1 class="text-3xl font-bold">Agenda</h1>

    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex-1 min-w-[240px]">
        <label class="block text-sm font-medium mb-1">Recherche</label>
        <input v-model="q" type="search" class="w-full rounded-lg border px-3 py-2" placeholder="Rechercher" />
      </div>
      <div class="min-w-[220px]">
        <label class="block text-sm font-medium mb-1">Ville</label>
        <input v-model.trim="city" class="w-full rounded-lg border px-3 py-2" placeholder="Ville" />
      </div>
      <div class="min-w-[220px]">
        <label class="block text-sm font-medium mb-1">Catégorie</label>
        <input v-model.trim="category" class="w-full rounded-lg border px-3 py-2" placeholder="Catégorie" />
      </div>
      <div class="min-w-[220px]">
        <label class="block text-sm font-medium mb-1">Du</label>
        <input v-model="from" type="date" class="w-full rounded-lg border px-3 py-2" />
      </div>
      <div class="min-w-[220px]">
        <label class="block text-sm font-medium mb-1">Au</label>
        <input v-model="to" type="date" class="w-full rounded-lg border px-3 py-2" />
      </div>
    </div>

    <div class="mt-6">
      <div v-if="loading" class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
      <div v-else-if="error" class="text-red-600 text-sm">{{ error }}</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="e in items" :key="e.id">
          <template #media>
            <EBImage :src="e.cover_image_url || eventThumb" :alt="e.title" :width="1200" :height="900" aspect-ratio="4 / 3" />
          </template>
          <template #title>{{ e.title }}</template>
          {{ e.city }} · {{ e.start_date }} → {{ e.end_date }}
          <template #actions>
            <RouterLink :to="{ name: 'event-detail', params: { slug: e.slug } }" class="inline-block">
              <Button size="sm" variant="outline">Détails</Button>
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
import { onMounted, reactive, ref, watch, computed } from 'vue'
import { useHead } from '@vueuse/head'
import { useRoute, useRouter } from 'vue-router'
import { fetchEvents } from '@/lib/api'
import Card from '@/components/ui/Card.vue'
import Button from '@/components/ui/Button.vue'
import Loader from '@/components/ui/Loader.vue'
import EBImage from '@/components/media/EBImage.vue'
import eventThumb from '@/assets/brand/images/thumbs/event-thumb.png'

useHead({ title: 'Agenda — ExploreBenin360' })

const route = useRoute(); const router = useRouter()
const q = ref(route.query.q?.toString() || '')
const city = ref(route.query.city?.toString() || '')
const category = ref(route.query.category?.toString() || '')
const from = ref(route.query.from?.toString() || '')
const to = ref(route.query.to?.toString() || '')
const page = ref(route.query.page ? Number(route.query.page) : 1)
const per_page = ref(12)

const items = ref([])
const meta = reactive({ total: 0, current_page: 1, per_page: 12 })
const loading = ref(false)
const error = ref('')

const updateQuery = () => {
  const query = { q: q.value || undefined, city: city.value || undefined, category: category.value || undefined, from: from.value || undefined, to: to.value || undefined, page: page.value !== 1 ? page.value : undefined }
  router.replace({ query })
}

let tHandle
watch([q, city, category, from, to], () => { clearTimeout(tHandle); tHandle = setTimeout(() => { page.value = 1; updateQuery(); load() }, 350) })
watch(page, () => { updateQuery(); load() })

const load = async () => {
  loading.value = true; error.value = ''
  try {
    const { data, meta: m } = await fetchEvents({ q: q.value, city: city.value || undefined, category: category.value || undefined, from: from.value || undefined, to: to.value || undefined, page: page.value, per_page: per_page.value, sort: 'date' })
    items.value = data; Object.assign(meta, m)
  } catch (e) { error.value = 'Erreur de chargement' } finally { loading.value = false }
}

const maxPage = computed(() => Math.max(1, Math.ceil(meta.total / meta.per_page)))
const setPage = (p) => { if (p>=1 && p<=maxPage.value) page.value = p }

onMounted(load)
</script>
