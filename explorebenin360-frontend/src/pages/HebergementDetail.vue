<template>
  <div v-if="item">
    <BrandBanner :src="item.cover_image_url || bannerFallback" alt="" :title="item.title" :subtitle="`${item.city} — ${item.type}`" class="block" :priority="true">
      <template #overlay>
        <FavoriteToggle type="hebergement" :id="item.id" :entity="{ id: item.id, title: item.title, slug: item.slug, cover_image_url: item.cover_image_url, city: item.city, price_per_night: item.price_per_night, currency: item.currency }" />
      </template>
      <RouterLink to="/offerings" class="btn-base focus-ring h-10 px-5 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
        Réserver
      </RouterLink>
    </BrandBanner>

    <div class="container-px mx-auto py-8 space-y-6">
      <div class="prose dark:prose-invert max-w-none">
        <p>{{ item.description }}</p>
      </div>
      <EBImage :src="item.cover_image_url || placeholder" :alt="buildAlt('hebergement', item.title, item.city)" :width="1200" :height="800" class="rounded-[var(--radius-lg)]"/>

      <section>
        <h2 class="text-xl font-semibold mb-3">Équipements</h2>
        <AmenitiesIcons :amenities="item.amenities || []" />
      </section>

      <MapShell :markers="[{ lat: item.lat, lng: item.lng, title: item.title }]" />
    </div>
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
import BrandBanner from '@/components/ui/BrandBanner.vue'
import MapShell from '@/components/maps/MapShell.vue'
import AmenitiesIcons from '@/components/hebergements/AmenitiesIcons.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'
import { buildAlt } from '@/utils/a11y'
import EBImage from '@/components/media/EBImage.vue'

const route = useRoute()
const item = ref<any>(null)
const bannerFallback = '/src/assets/brand/images/hebergements/banner-default.png'
const placeholder = '/src/assets/brand/images/hebergements/banner-default.png'

onMounted(async () => {
  const slug = route.params.slug.toString()
  const { data } = await fetchAccommodation(slug)
  item.value = data
  useHead({ title: `${data.title} — ExploreBenin360`, meta: [ { name: 'description', content: data.description?.slice(0,150) } ] })
})
</script>
