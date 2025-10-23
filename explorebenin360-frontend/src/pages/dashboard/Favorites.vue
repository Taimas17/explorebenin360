<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="" :title="t('dashboard.favorites')" class="mb-6" />

    <div class="flex items-center justify-between mb-4">
      <div class="flex gap-2">
        <RouterLink to="/dashboard/reservations" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.my_reservations') }}</RouterLink>
        <RouterLink to="/dashboard/messages" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.messages') }}</RouterLink>
      </div>
      <RouterLink to="/profile" class="btn-base focus-ring h-9 px-3 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('dashboard.update_profile') }}</RouterLink>
    </div>

    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else>
      <EmptyState v-if="totalCount === 0" variant="favorites" :title="t('dashboard.no_favorites')">
        <RouterLink to="/explorer" class="btn-base focus-ring h-9 px-3 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('dashboard.explore_now') }}</RouterLink>
      </EmptyState>

      <div v-else class="space-y-8">
        <section v-if="data.places.length">
          <h2 class="text-xl font-semibold mb-3">{{ t('dashboard.favorite_places') }}</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            <Card v-for="p in data.places" :key="p.id">
              <template #media>
                <EBImage :src="p.cover_image_url || thumbs.destination" :alt="p.title" :width="800" :height="600" aspect-ratio="4 / 3" />
                <div class="absolute top-2 right-2">
                  <FavoriteToggle type="destination" :id="p.id" size="sm" :entity="p" />
                </div>
              </template>
              <template #title>{{ p.title }}</template>
              <template #actions>
                <RouterLink :to="`/destinations/${p.slug}`" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('common.details') }}</RouterLink>
              </template>
            </Card>
          </div>
        </section>

        <section v-if="data.accommodations.length">
          <h2 class="text-xl font-semibold mb-3">{{ t('dashboard.favorite_stays') }}</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            <Card v-for="h in data.accommodations" :key="h.id">
              <template #media>
                <EBImage :src="h.cover_image_url || thumbs.hebergement" :alt="h.title" :width="800" :height="600" aspect-ratio="4 / 3" />
                <div class="absolute top-2 right-2">
                  <FavoriteToggle type="hebergement" :id="h.id" size="sm" :entity="h" />
                </div>
              </template>
              <template #title>{{ h.title }}</template>
              <template #actions>
                <RouterLink :to="`/hebergements/${h.slug}`" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('common.details') }}</RouterLink>
              </template>
            </Card>
          </div>
        </section>

        <section v-if="data.articles.length">
          <h2 class="text-xl font-semibold mb-3">{{ t('dashboard.favorite_articles') }}</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            <Card v-for="a in data.articles" :key="a.id">
              <template #media>
                <EBImage :src="a.cover_image_url || thumbs.article" :alt="a.title" :width="800" :height="600" aspect-ratio="4 / 3" />
                <div class="absolute top-2 right-2">
                  <FavoriteToggle type="article" :id="a.id" size="sm" :entity="a" />
                </div>
              </template>
              <template #title>{{ a.title }}</template>
              <template #actions>
                <RouterLink :to="`/blog/${a.slug}`" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('common.details') }}</RouterLink>
              </template>
            </Card>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import Card from '@/components/ui/Card.vue'
import EBImage from '@/components/media/EBImage.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'
import { fetchFavorites } from '@/lib/services/favorites'
import travelerHeader from '@/assets/brand/images/dashboard/traveler/header.png'

const { t } = useI18n()
const banner = travelerHeader
const thumbs = {
  destination: '/src/assets/brand/images/thumbs/destination-thumb.png',
  hebergement: '/src/assets/brand/images/thumbs/hebergement-thumb.png',
  article: '/src/assets/brand/images/thumbs/article-thumb.png',
}

const data = ref({ places: [], accommodations: [], articles: [] })
const loading = ref(false)
const totalCount = computed(() => data.value.places.length + data.value.accommodations.length + data.value.articles.length)

onMounted(async () => {
  loading.value = true
  try { data.value = await fetchFavorites() } finally { loading.value = false }
})
</script>
