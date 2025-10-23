<template>
  <div class="container-px mx-auto py-8 space-y-8">
    <h1 class="text-3xl font-bold">{{ t('favorites.title') }}</h1>

    <template v-if="isEmpty">
      <EmptyState variant="favorites" :title="t('favorites.empty_title')" :description="t('favorites.empty_desc')">
        <RouterLink to="/explorer" class="btn-base focus-ring h-10 px-5 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('favorites.explore_cta') }}</RouterLink>
      </EmptyState>
    </template>

    <template v-else>
      <section v-if="destinations.length">
        <h2 class="text-xl font-semibold mb-4">{{ t('favorites.destinations') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card v-for="p in destinations" :key="p.id">
            <template #media>
              <EBImage :src="p.cover_image_url || thumbs.destination" :alt="p.title" :width="1200" :height="900" aspect-ratio="4 / 3" />
              <div class="absolute top-2 right-2">
                <FavoriteToggle type="destination" :id="p.id" size="sm" :entity="p" />
              </div>
            </template>
            <template #title>{{ p.title }}</template>
            {{ p.city }}
            <template #actions>
              <RouterLink :to="{ name: 'destination-detail', params: { slug: p.slug } }" class="inline-block">
                <Button variant="secondary" size="sm">{{ t('common.details') }}</Button>
              </RouterLink>
            </template>
          </Card>
        </div>
      </section>

      <section v-if="hebergements.length">
        <h2 class="text-xl font-semibold mb-4">{{ t('favorites.hebergements') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card v-for="h in hebergements" :key="h.id">
            <template #media>
              <EBImage :src="h.cover_image_url || thumbs.hebergement" :alt="h.title" :width="1200" :height="900" aspect-ratio="4 / 3" />
              <div class="absolute top-2 right-2">
                <FavoriteToggle type="hebergement" :id="h.id" size="sm" :entity="h" />
              </div>
            </template>
            <template #title>{{ h.title }}</template>
            {{ h.city }} · {{ h.price_per_night?.toLocaleString?.() }} {{ h.currency }}
            <template #actions>
              <RouterLink :to="{ name: 'hebergement-detail', params: { slug: h.slug } }" class="inline-block">
                <Button variant="secondary" size="sm">{{ t('common.details') }}</Button>
              </RouterLink>
            </template>
          </Card>
        </div>
      </section>

      <section v-if="articles.length">
        <h2 class="text-xl font-semibold mb-4">{{ t('favorites.articles') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Card v-for="a in articles" :key="a.id">
            <template #media>
              <EBImage :src="a.cover_image_url || thumbs.article" :alt="a.title" :width="1200" :height="630" aspect-ratio="1200 / 630" />
              <div class="absolute top-2 right-2">
                <FavoriteToggle type="article" :id="a.id" size="sm" :entity="a" />
              </div>
            </template>
            <template #title>{{ a.title }}</template>
            {{ a.excerpt }}
            <template #actions>
              <RouterLink :to="{ name: 'article-detail', params: { slug: a.slug } }" class="inline-block">
                <Button variant="secondary" size="sm">{{ t('common.details') }}</Button>
              </RouterLink>
            </template>
          </Card>
        </div>
      </section>

      <section v-if="guides.length">
        <h2 class="text-xl font-semibold mb-4">{{ t('favorites.guides') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card v-for="g in guides" :key="g.id">
            <template #media>
              <div class="aspect-[4/3] flex items-center justify-center bg-black/5 dark:bg-white/5">
                <img v-if="g.avatar_url" :src="g.avatar_url" :alt="g.name" class="w-20 h-20 rounded-full object-cover" />
                <AvatarFallback v-else :name="g.name" :size="72" />
                <div class="absolute top-2 right-2">
                  <FavoriteToggle type="guide" :id="g.id" size="sm" :entity="g" />
                </div>
              </div>
            </template>
            <template #title>{{ g.name }}</template>
            {{ g.city }}
          </Card>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { RouterLink } from 'vue-router'
import { useFavoritesStore } from '@/stores/favorites'
import Card from '@/components/ui/Card.vue'
import Button from '@/components/ui/Button.vue'
import EBImage from '@/components/media/EBImage.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import FavoriteToggle from '@/components/ui/FavoriteToggle.vue'
import AvatarFallback from '@/components/ui/AvatarFallback.vue'

const { t } = useI18n()
const fav = useFavoritesStore()

const thumbs = {
  destination: '/src/assets/brand/images/thumbs/destination-thumb.png',
  hebergement: '/src/assets/brand/images/thumbs/hebergement-thumb.png',
  guide: '/src/assets/brand/images/thumbs/guide-thumb.png',
  article: '/src/assets/brand/images/thumbs/article-thumb.png',
}

const destinations = computed(() => Array.from(fav.items.destination).map(id => fav.entities.destination[id]).filter(Boolean))
const hebergements = computed(() => Array.from(fav.items.hebergement).map(id => fav.entities.hebergement[id]).filter(Boolean))
const articles = computed(() => Array.from(fav.items.article).map(id => fav.entities.article[id]).filter(Boolean))
const guides = computed(() => Array.from(fav.items.guide).map(id => fav.entities.guide[id]).filter(Boolean))

const isEmpty = computed(() => !destinations.value.length && !hebergements.value.length && !articles.value.length && !guides.value.length)
</script>
