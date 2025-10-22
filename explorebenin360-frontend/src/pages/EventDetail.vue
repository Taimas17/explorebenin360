<template>
  <div class="container-px mx-auto py-8 space-y-6" v-if="item">
    <h1 class="text-3xl font-bold">{{ item.title }}</h1>
    <p class="text-[color:var(--color-text-muted)]">{{ item.city }} · {{ item.start_date }} → {{ item.end_date }}</p>
    <EBImage :src="item.cover_image_url || placeholder" :alt="item.title" :width="1200" :height="630" class="rounded-[var(--radius-lg)]"/>
    <div class="prose dark:prose-invert max-w-none">
      <p>{{ item.description }}</p>
    </div>
  </div>
  <div class="container-px mx-auto py-16" v-else>
    <div class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useHead } from '@vueuse/head'
import { fetchEvent } from '@/lib/api'
import Loader from '@/components/ui/Loader.vue'
import EBImage from '@/components/media/EBImage.vue'

const route = useRoute()
const item = ref(null)
const placeholder = 'https://picsum.photos/seed/event/1200/630'

onMounted(async () => {
  const slug = route.params.slug.toString()
  const { data } = await fetchEvent(slug)
  item.value = data
  useHead({ title: `${data.title} — ExploreBenin360`, meta: [ { name: 'description', content: data.description?.slice(0,150) } ] })
})
</script>
