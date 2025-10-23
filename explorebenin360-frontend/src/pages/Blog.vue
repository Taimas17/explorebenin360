<template>
  <div class="container-px mx-auto py-8 space-y-6">
    <BrandBanner :src="banner" alt="Bannière Blog" :title="t('nav.blog')" class="mb-6" :priority="true" />

    <div class="flex flex-wrap gap-3 items-end">
      <div class="flex-1 min-w-[240px]">
        <label class="block text-sm font-medium mb-1">Recherche</label>
        <input v-model="q" type="search" class="w-full rounded-lg border px-3 py-2" placeholder="Rechercher" />
      </div>
      <div class="min-w-[220px]">
        <label class="block text-sm font-medium mb-1">Catégorie</label>
        <input v-model.trim="category" class="w-full rounded-lg border px-3 py-2" placeholder="Catégorie" />
      </div>
      <div class="min-w-[220px]">
        <label class="block text-sm font-medium mb-1">Tag</label>
        <input v-model.trim="tag" class="w-full rounded-lg border px-3 py-2" placeholder="Tag" />
      </div>
    </div>

    <div class="mt-6">
      <div v-if="loading" class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
      <div v-else-if="error" class="text-red-600 text-sm">{{ error }}</div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card v-for="a in items" :key="a.id">
          <template #media>
            <EBImage :src="a.cover_image_url || banner" :alt="a.title" :width="1200" :height="630" aspect-ratio="1200 / 630" class="w-full h-auto" sizes="(max-width: 768px) 100vw, 50vw" />
          </template>
          <template #title>{{ a.title }}</template>
          {{ a.excerpt }}
          <template #actions>
            <RouterLink :to="{ name: 'article-detail', params: { slug: a.slug } }" class="inline-block">
              <Button size="sm" variant="outline">Lire</Button>
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
import { useI18n } from 'vue-i18n'
import { useHead } from '@vueuse/head'
import { useRoute, useRouter } from 'vue-router'
import { fetchArticles } from '@/lib/api'
import Card from '@/components/ui/Card.vue'
import Button from '@/components/ui/Button.vue'
import Loader from '@/components/ui/Loader.vue'
import EBImage from '@/components/media/EBImage.vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'

const { t } = useI18n()
useHead({ title: 'Blog — ExploreBenin360', meta: [ { name: 'description', content: t('brand.baseline') }, { property: 'og:image', content: '/og-image.png' } ] })
const banner = '/src/assets/brand/images/blog/cover-default.png'

const route = useRoute(); const router = useRouter()
const q = ref(route.query.q?.toString() || '')
const category = ref(route.query.category?.toString() || '')
const tag = ref(route.query.tag?.toString() || '')
const page = ref(route.query.page ? Number(route.query.page) : 1)
const per_page = ref(10)

const items = ref([])
const meta = reactive({ total: 0, current_page: 1, per_page: 10 })
const loading = ref(false)
const error = ref('')

const updateQuery = () => {
  const query = {
    q: q.value || undefined,
    category: category.value || undefined,
    tag: tag.value || undefined,
    page: page.value !== 1 ? page.value : undefined,
  }
  router.replace({ query })
}

let tHandle
watch([q, category, tag], () => { clearTimeout(tHandle); tHandle = setTimeout(() => { page.value = 1; updateQuery(); load() }, 350) })
watch(page, () => { updateQuery(); load() })

const load = async () => {
  loading.value = true; error.value = ''
  try {
    const { data, meta: m } = await fetchArticles({ q: q.value, category: category.value || undefined, tag: tag.value || undefined, page: page.value, per_page: per_page.value, sort: 'recent' })
    items.value = data; Object.assign(meta, m)
  } catch (e) { error.value = 'Erreur de chargement' } finally { loading.value = false }
}

const maxPage = computed(() => Math.max(1, Math.ceil(meta.total / meta.per_page)))
const setPage = (p) => { if (p>=1 && p<=maxPage.value) page.value = p }

onMounted(load)
</script>
