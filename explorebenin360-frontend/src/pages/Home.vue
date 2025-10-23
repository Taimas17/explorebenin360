<template>
  <EBGallery :items="homeHero" variant="hero">
    <template #hero-content>
      <h1 class="text-4xl md:text-6xl font-bold text-white">ExploreBenin<span class="text-[color:var(--color-secondary)]">360</span></h1>
      <p class="mt-2 text-white/90 text-lg">{{ t('brand.baseline') }}</p>
      <div class="mt-6 flex gap-3">
        <Button variant="primary" size="lg">
          {{ t('hero.cta') }}
        </Button>
        <Button variant="outline" size="lg">
          <span class="flex items-center gap-2">{{ t('nav.destinations') }} <Icon name="ArrowRight"/></span>
        </Button>
      </div>
    </template>
  </EBGallery>

  <main class="container-px mx-auto py-10 space-y-12">
    <section>
      <h2 class="text-2xl font-bold mb-4">Galerie</h2>
      <EBGallery :items="heroItems" />
    </section>

    <section>
      <h2 class="text-2xl font-bold mb-4">{{ t('sections.popular_destinations') }}</h2>
      <div v-if="loading.places" class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
      <div v-else-if="errors.places" class="text-red-600 text-sm">{{ errors.places }}</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="p in places" :key="p.id">
          <template #media>
            <EBImage :src="p.cover_image_url || thumbs.destination" :alt="p.title" :width="1200" :height="900" aspect-ratio="4 / 3" />
            <div class="absolute top-2 right-2">
              <FavoriteToggle type="destination" :id="p.id" size="sm" :entity="{ id: p.id, title: p.title, slug: p.slug, cover_image_url: p.cover_image_url, city: p.city }" />
            </div>
          </template>
          <template #title>{{ p.title }}</template>
          {{ p.city }}
          <template #actions>
            <RouterLink :to="{ name: 'destination-detail', params: { slug: p.slug } }" class="inline-block">
              <Button variant="secondary" size="sm">{{ t('hero.cta') }}</Button>
            </RouterLink>
          </template>
        </Card>
      </div>
    </section>

    <section>
      <h2 class="text-2xl font-bold mb-4">Hébergements</h2>
      <div v-if="loading.accommodations" class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
      <div v-else-if="errors.accommodations" class="text-red-600 text-sm">{{ errors.accommodations }}</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="h in accommodations" :key="h.id">
          <template #media>
            <EBImage :src="h.cover_image_url || thumbs.hebergement" :alt="h.title" :width="1200" :height="900" aspect-ratio="4 / 3" />
            <div class="absolute top-2 right-2">
              <FavoriteToggle type="hebergement" :id="h.id" size="sm" :entity="{ id: h.id, title: h.title, slug: h.slug, cover_image_url: h.cover_image_url, city: h.city, price_per_night: h.price_per_night, currency: h.currency }" />
            </div>
          </template>
          <template #title>{{ h.title }}</template>
          {{ h.city }} · {{ h.price_per_night.toLocaleString() }} {{ h.currency }} / nuit
          <template #actions>
            <RouterLink :to="{ name: 'hebergement-detail', params: { slug: h.slug } }" class="inline-block">
              <Button variant="secondary" size="sm">Voir</Button>
            </RouterLink>
          </template>
        </Card>
      </div>
    </section>

    <section>
      <h2 class="text-2xl font-bold mb-4">Guides</h2>
      <div v-if="loading.guides" class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
      <div v-else-if="errors.guides" class="text-red-600 text-sm">{{ errors.guides }}</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="g in guides" :key="g.id">
          <template #media>
            <EBImage :src="g.avatar_url || thumbs.guide" :alt="g.name" :width="1200" :height="900" aspect-ratio="4 / 3" />
            <div class="absolute top-2 right-2">
              <FavoriteToggle type="guide" :id="g.id" size="sm" :entity="{ id: g.id, name: g.name, slug: g.slug, avatar_url: g.avatar_url, city: g.city }" />
            </div>
          </template>
          <template #title>{{ g.name }}</template>
          {{ g.city }}
        </Card>
      </div>
    </section>

    <section>
      <h2 class="text-2xl font-bold mb-4">Blog</h2>
      <div v-if="loading.articles" class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
      <div v-else-if="errors.articles" class="text-red-600 text-sm">{{ errors.articles }}</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="a in articles" :key="a.id">
          <template #media>
            <EBImage :src="a.cover_image_url || thumbs.article" :alt="a.title" :width="1200" :height="900" aspect-ratio="4 / 3" />
            <div class="absolute top-2 right-2">
              <FavoriteToggle type="article" :id="a.id" size="sm" :entity="{ id: a.id, title: a.title, slug: a.slug, cover_image_url: a.cover_image_url, excerpt: a.excerpt }" />
            </div>
          </template>
          <template #title>{{ a.title }}</template>
          {{ a.excerpt }}
          <template #actions>
            <RouterLink :to="{ name: 'article-detail', params: { slug: a.slug } }" class="inline-block">
              <Button variant="secondary" size="sm">Lire</Button>
            </RouterLink>
          </template>
        </Card>
      </div>
    </section>

    <section>
      <h2 class="text-2xl font-bold mb-4">Agenda</h2>
      <div v-if="loading.events" class="flex gap-3 items-center"><Loader/> <span>Chargement…</span></div>
      <div v-else-if="errors.events" class="text-red-600 text-sm">{{ errors.events }}</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="e in events" :key="e.id">
          <template #media>
            <EBImage :src="e.cover_image_url || thumbs.event" :alt="e.title" :width="1200" :height="900" aspect-ratio="4 / 3" />
          </template>
          <template #title>{{ e.title }}</template>
          {{ e.city }} · {{ e.start_date }}
          <template #actions>
            <RouterLink :to="{ name: 'event-detail', params: { slug: e.slug } }" class="inline-block">
              <Button variant="secondary" size="sm">Détails</Button>
            </RouterLink>
          </template>
        </Card>
      </div>
    </section>

    <section>
      <h2 class="text-2xl font-bold mb-4">Panorama 360°</h2>
      <EB360Viewer :src="svg('Panorama')" alt="Vue 360" />
    </section>
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useHead } from '@vueuse/head'
import { fetchPlaces, fetchAccommodations, fetchGuides, fetchArticles, fetchEvents } from '@/lib/api'
import Button from '@/components/ui/Button.vue'
import Card from '@/components/ui/Card.vue'
import Loader from '@/components/ui/Loader.vue'
import Icon from '@/components/ui/Icon.vue'
import EBGallery from '@/components/media/EBGallery.vue'
import EB360Viewer from '@/components/media/EB360Viewer.vue'
import EBImage from '@/components/media/EBImage.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'

const { t } = useI18n()
useHead({ title: 'ExploreBenin360 — Accueil', meta: [ { name: 'description', content: t('brand.baseline') }, { property: 'og:image', content: '/og-image.png' } ], link: [ { rel: 'preload', as: 'image', href: '/src/assets/brand/images/home/hero-1.png', imagesrcset: '/src/assets/brand/images/home/hero-1.png 1x', fetchpriority: 'high' } ] })

const svg = (label, from = '#FF6B35', to = '#FFD166') =>
  'data:image/svg+xml;utf8,' + encodeURIComponent(`
  <svg xmlns='http://www.w3.org/2000/svg' width='1600' height='900' viewBox='0 0 1600 900'>
    <defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0%' stop-color='${from}'/><stop offset='100%' stop-color='${to}'/></linearGradient></defs>
    <rect width='1600' height='900' fill='url(#g)'/>
    <text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='64' font-family='Poppins, Inter, sans-serif' fill='#0b0f16'>${label}</text>
  </svg>`)

const homeHero = [
  { src: '/src/assets/brand/images/home/hero-1.png', alt: 'Plage du Bénin au lever du soleil' },
  { src: '/src/assets/brand/images/home/hero-2.png', alt: 'Village lacustre de Ganvié au coucher du soleil' },
  { src: '/src/assets/brand/images/home/hero-3.png', alt: 'Savane du parc de la Pendjari' },
]

const heroItems = [
  { src: '/src/assets/brand/images/home/hero-1.png', alt: 'Plage — ExploreBenin360', caption: 'Côte béninoise' },
  { src: '/src/assets/brand/images/home/hero-2.png', alt: 'Ganvié — ExploreBenin360', caption: 'Ganvié' },
  { src: '/src/assets/brand/images/home/hero-3.png', alt: 'Pendjari — ExploreBenin360', caption: 'Pendjari' },
]

const loading = reactive({ places: true, accommodations: true, guides: true, articles: true, events: true })
const errors = reactive({ places: '', accommodations: '', guides: '', articles: '', events: '' })
const places = ref<any[]>([])
const accommodations = ref<any[]>([])
const guides = ref<any[]>([])
const articles = ref<any[]>([])
const events = ref<any[]>([])

const thumbs = {
  destination: '/src/assets/brand/images/thumbs/destination-thumb.png',
  hebergement: '/src/assets/brand/images/thumbs/hebergement-thumb.png',
  guide: '/src/assets/brand/images/thumbs/guide-thumb.png',
  article: '/src/assets/brand/images/thumbs/article-thumb.png',
  event: '/src/assets/brand/images/thumbs/event-thumb.png',
}

onMounted(async () => {
  try { const { data } = await fetchPlaces({ featured: true, per_page: 6 }); places.value = data } catch(e){ errors.places = 'Erreur de chargement'; } finally { loading.places = false }
  try { const { data } = await fetchAccommodations({ featured: true, per_page: 6 }); accommodations.value = data } catch(e){ errors.accommodations = 'Erreur de chargement'; } finally { loading.accommodations = false }
  try { const { data } = await fetchGuides({ per_page: 3 }); guides.value = data } catch(e){ errors.guides = 'Erreur de chargement'; } finally { loading.guides = false }
  try { const { data } = await fetchArticles({ sort: 'recent', per_page: 3 }); articles.value = data } catch(e){ errors.articles = 'Erreur de chargement'; } finally { loading.articles = false }
  try { const { data } = await fetchEvents({ featured: true, per_page: 3 }); events.value = data } catch(e){ errors.events = 'Erreur de chargement'; } finally { loading.events = false }
})
</script>
