<template>
  <div v-if="item">
    <EBGallery v-if="galleryItems.length" :items="galleryItems" variant="hero">
      <template #hero-content>
        <div class="flex items-end justify-between gap-4">
          <div>
            <h1 class="text-3xl md:text-4xl font-bold text-white drop-shadow">{{ item.title }}</h1>
            <p class="mt-2 text-white/90">{{ item.city }} — {{ item.type }}</p>
          </div>
          <div class="flex items-center gap-3">
            <FavoriteToggle type="hebergement" :id="item.id" :entity="{ id: item.id, title: item.title, slug: item.slug, cover_image_url: item.cover_image_url, city: item.city, price_per_night: item.price_per_night, currency: item.currency }" />
            <RouterLink to="/offerings" class="btn-base focus-ring h-10 px-5 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
              Réserver
            </RouterLink>
          </div>
        </div>
      </template>
    </EBGallery>

    <div v-else class="space-y-4">
      <div class="flex items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold">{{ item.title }}</h1>
          <p class="text-[color:var(--color-text-muted)]">{{ item.city }} — {{ item.type }}</p>
        </div>
        <div class="flex items-center gap-3">
          <FavoriteToggle type="hebergement" :id="item.id" :entity="{ id: item.id, title: item.title, slug: item.slug, cover_image_url: item.cover_image_url, city: item.city, price_per_night: item.price_per_night, currency: item.currency }" />
          <RouterLink to="/offerings" class="btn-base focus-ring h-10 px-5 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
            Réserver
          </RouterLink>
        </div>
      </div>
      <EBImage :src="item.cover_image_url || placeholder" :alt="buildAlt('hebergement', item.title, item.city)" :width="1200" :height="800" class="rounded-[var(--radius-lg)]"/>
    </div>

    <div class="container-px mx-auto py-8 space-y-6">
      <div class="prose dark:prose-invert max-w-none">
        <p>{{ item.description }}</p>
      </div>

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
<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useHead } from '@vueuse/head'
import { fetchAccommodation } from '@/lib/api'
import Loader from '@/components/ui/Loader.vue'
import EBImage from '@/components/media/EBImage.vue'
import EBGallery from '@/components/media/EBGallery.vue'
import MapShell from '@/components/maps/MapShell.vue'
import AmenitiesIcons from '@/components/hebergements/AmenitiesIcons.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'
import { buildAlt } from '@/utils/a11y'
import { mapToGalleryItems } from '@/utils/media'

const route = useRoute()
const item = ref<any>(null)
const placeholder = '/src/assets/brand/images/hebergements/banner-default.png'

const galleryItems = computed(() => item.value ? mapToGalleryItems(item.value, { title: item.value.title, fallbackUrl: placeholder }) : [])

onMounted(async () => {
  const slug = route.params.slug.toString()
  const { data } = await fetchAccommodation(slug)
  item.value = data
  useHead({ title: `${data.title} — ExploreBenin360`, meta: [ { name: 'description', content: data.description?.slice(0,150) } ] })
})
</script>
