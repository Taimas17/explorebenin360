<template>
  <div class="container-px mx-auto py-8 space-y-6" v-if="item">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold">{{ item.title }}</h1>
        <p class="text-[color:var(--color-text-muted)]">{{ item.city }} — {{ item.type }}</p>
      </div>
      <RouterLink to="/offerings" class="btn-base focus-ring h-10 px-5 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
        Réserver
      </RouterLink>
    </div>

    <EBImage :src="item.cover_image_url || placeholder" :alt="item.title" :width="1200" :height="800" aspect-ratio="3 / 2" sizes="100vw" :priority="true" class="rounded-[var(--radius-lg)]"/>

    <div class="prose dark:prose-invert max-w-none">
      <p>{{ item.description }}</p>
    </div>

    <MapShell :markers="[{ lat: item.lat, lng: item.lng, title: item.title }]" />
  </div>
  <div class="container-px mx-auto py-16" v-else>
    <div class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useHead } from '@vueuse/head'
import { fetchAccommodation } from '@/lib/api'
import Loader from '@/components/ui/Loader.vue'
import EBImage from '@/components/media/EBImage.vue'
import MapShell from '@/components/maps/MapShell.vue'

const route = useRoute()
const item = ref(null)
const placeholder = 'https://picsum.photos/seed/accommodation/1200/800'

onMounted(async () => {
  const slug = route.params.slug.toString()
  const { data } = await fetchAccommodation(slug)
  item.value = data
  useHead({ title: `${data.title} — ExploreBenin360`, meta: [ { name: 'description', content: data.description?.slice(0,150) } ] })
})
</script>
