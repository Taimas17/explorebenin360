<template>
  <div class="container-px mx-auto py-8 space-y-6" v-if="item">
    <h1 class="text-3xl font-bold">{{ item.title }}</h1>
    <p class="text-[color:var(--color-text-muted)]">{{ item.city }} · {{ item.start_date }} → {{ item.end_date }}</p>
    <EBImage :src="item.cover_image_url || placeholder" :alt="item.title" :width="1200" :height="630" class="rounded-[var(--radius-lg)]"/>
    <div class="prose dark:prose-invert max-w-none">
      <p>{{ item.description }}</p>
    </div>

    <section class="space-y-4">
      <h2 class="text-xl font-semibold">Avis</h2>
      <RatingSummary v-if="item.reviews_summary" :summary="item.reviews_summary" />
      <ReviewForm v-if="isAuthenticated" :reviewable-type="'App\\\\Models\\\\Event'" :reviewable-id="item.id" @success="load" />
      <ReviewsList :reviewable-type="'App\\\\Models\\\\Event'" :reviewable-id="item.id" @updated="load" />
    </section>
  </div>
  <div class="container-px mx-auto py-16" v-else>
    <div class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
  </div>
</template>
<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useHead } from '@vueuse/head'
import { setPageMeta } from '@/utils/meta'
import { fetchEvent } from '@/lib/api'
import RatingSummary from '@/components/reviews/RatingSummary.vue'
import ReviewsList from '@/components/reviews/ReviewsList.vue'
import ReviewForm from '@/components/reviews/ReviewForm.vue'
import { useAuthStore } from '@/stores/auth'
import Loader from '@/components/ui/Loader.vue'
import EBImage from '@/components/media/EBImage.vue'
import eventThumb from '@/assets/brand/images/thumbs/event-thumb.png'

const route = useRoute()
const item = ref<any>(null)
const placeholder = eventThumb
const auth = useAuthStore()
const isAuthenticated = computed(() => auth.isAuthenticated)

onMounted(load)

async function load() {
  const slug = route.params.slug.toString()
  const { data } = await fetchEvent(slug)
  item.value = data
  setPageMeta({ title: data.seo?.title || `${data.title} — ExploreBenin360`, description: data.seo?.description || (data.description || '').slice(0,150), path: data.seo?.path || `/agenda/${data.slug}`, image: data.cover_image_url })
}
</script>
