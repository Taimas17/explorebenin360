<template>
  <div class="container-px mx-auto py-8 space-y-6" v-if="item">
    <BrandBanner :src="item.cover_image_url || placeholder" :alt="item.title" :title="item.title" :subtitle="item.city + ' — ' + item.type" class="mb-4" :priority="true">
      <template #overlay>
        <FavoriteToggle type="destination" :id="item.id" :entity="{ id: item.id, title: item.title, slug: item.slug, cover_image_url: item.cover_image_url, city: item.city }" />
      </template>
    </BrandBanner>

    <div class="prose dark:prose-invert max-w-none" v-html="item.description"></div>

    <MapShell :markers="[{ lat: item.lat, lng: item.lng, title: item.title }]" />
  </div>
  <div class="container-px mx-auto py-16" v-else>
    <div class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useHead } from '@vueuse/head'
import { fetchPlace } from '@/lib/api'
import Loader from '@/components/ui/Loader.vue'
import EBImage from '@/components/media/EBImage.vue'
import MapShell from '@/components/maps/MapShell.vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'

const route = useRoute()
const item = ref(null)
const placeholder = 'https://picsum.photos/seed/place/1200/800'

onMounted(async () => {
  const slug = route.params.slug.toString()
  const { data } = await fetchPlace(slug)
  item.value = data
  useHead({ title: `${data.title} — ExploreBenin360`, meta: [ { name: 'description', content: data.excerpt || data.description?.slice(0,150) } ] })
})
</script>
