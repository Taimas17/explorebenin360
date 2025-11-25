<template>
  <div class="container-px mx-auto py-8 space-y-6" v-if="item">
    <EBGallery v-if="galleryItems.length" :items="galleryItems" variant="hero" class="mb-4">
      <template #hero-content>
        <div class="flex items-end justify-between gap-4">
          <div class="max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-bold text-white drop-shadow">{{ item.title }}</h1>
            <p class="mt-2 text-white/90">{{ item.city }} — {{ item.type }}</p>
          </div>
          <FavoriteToggle type="destination" :id="item.id" :entity="{ id: item.id, title: item.title, slug: item.slug, cover_image_url: item.cover_image_url, city: item.city }" />
        </div>
      </template>
    </EBGallery>
    <BrandBanner v-else :src="item.cover_image_url || placeholder" alt="" :title="item.title" :subtitle="item.city + ' — ' + item.type" class="mb-4" />

    <div class="prose dark:prose-invert max-w-none" v-html="sanitizedDescription"></div>

    <div class="mt-8">
      <ReviewsList reviewable-type="places" :reviewable-id="item.id" />
    </div>

    <MapShell :markers="[{ lat: item.lat, lng: item.lng, title: item.title }]" />
  </div>
  <div class="container-px mx-auto py-16" v-else>
    <div class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useHead } from '@vueuse/head'
import { setPageMeta } from '@/utils/meta'
import { fetchPlace } from '@/lib/api'
import Loader from '@/components/ui/Loader.vue'
import MapShell from '@/components/maps/MapShell.vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'
import EBGallery from '@/components/media/EBGallery.vue'
import { mapToGalleryItems } from '@/utils/media'
import { sanitizeHtml } from '@/utils/sanitize'
import ReviewsList from '@/components/reviews/ReviewsList.vue'
import destinationsBanner from '@/assets/brand/images/destinations/banner-default.png'

const route = useRoute()
const item = ref<any>(null)
const placeholder = destinationsBanner

const galleryItems = computed(() => item.value ? mapToGalleryItems(item.value, { title: item.value.title, fallbackUrl: placeholder }) : [])

const sanitizedDescription = computed(() => sanitizeHtml(item.value?.description))

onMounted(async () => {
  const slug = route.params.slug.toString()
  const { data } = await fetchPlace(slug)
  item.value = data
  setPageMeta({ title: data.seo?.title || `${data.title} — ExploreBenin360`, description: data.seo?.description || (data.excerpt || (data.description || '').replace(/<[^>]+>/g,'').slice(0,150)), path: data.seo?.path || `/destinations/${data.slug}`, image: data.cover_image_url })
})
</script>
