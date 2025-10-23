<template>
  <div class="container-px mx-auto py-8 space-y-6" v-if="item">
    <BrandBanner :src="item.cover_image_url || placeholder" :alt="item.title" :title="item.title" :subtitle="item.author_name + ' — ' + formatDate(item.published_at)">
      <template #overlay>
        <FavoriteToggle type="article" :id="item.id" :entity="{ id: item.id, title: item.title, slug: item.slug, cover_image_url: item.cover_image_url, excerpt: item.excerpt }" />
      </template>
    </BrandBanner>
    <article class="prose dark:prose-invert max-w-none" v-html="item.body"></article>
  </div>
  <div class="container-px mx-auto py-16" v-else>
    <div class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useHead } from '@vueuse/head'
import { fetchArticle } from '@/lib/api'
import Loader from '@/components/ui/Loader.vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'

const route = useRoute()
const item = ref(null)
const placeholder = '/src/assets/brand/images/blog/cover-default.png'
const formatDate = (s) => s ? new Date(s).toLocaleDateString() : ''

onMounted(async () => {
  const slug = route.params.slug.toString()
  const { data } = await fetchArticle(slug)
  item.value = data
  useHead({ title: `${data.title} — ExploreBenin360`, meta: [ { name: 'description', content: data.excerpt?.slice(0,150) }, { property: 'og:image', content: data.cover_image_url || placeholder } ] })
})
</script>
