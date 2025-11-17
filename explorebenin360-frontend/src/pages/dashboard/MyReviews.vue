<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="" :title="t('reviews.my_reviews')" class="mb-6" />
    
    <div v-if="loading" class="text-center py-8">
      <Loader />
    </div>
    
    <EmptyState 
      v-else-if="reviews.length === 0"
      variant="default"
      :title="t('reviews.no_reviews_yet')"
    >
      <RouterLink to="/dashboard/reservations" class="btn-base focus-ring h-9 px-3 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
        {{ t('reviews.view_bookings') }}
      </RouterLink>
    </EmptyState>
    
    <div v-else class="space-y-4">
      <div
        v-for="review in reviews"
        :key="review.id"
        class="border border-black/10 dark:border-white/10 rounded-lg p-4"
      >
        <div class="flex items-start justify-between mb-3">
          <div>
            <h3 class="font-medium">{{ review.offering.title }}</h3>
            <div class="text-xs text-[color:var(--color-text-muted)]">
              {{ formatDate(review.created_at) }}
            </div>
          </div>
          <Badge :variant="statusVariant(review.status)">
            {{ t(`reviews.status_${review.status}`) }}
          </Badge>
        </div>
        
        <StarRating :rating="review.rating" readonly size="sm" />
        <p class="text-sm mt-2">{{ review.comment }}</p>
        
        <div v-if="review.photos && review.photos.length > 0" class="mt-3 flex gap-2">
          <EBImage
            v-for="(photo, idx) in review.photos"
            :key="idx"
            :src="photo"
            :alt="`Photo ${idx + 1}`"
            :width="100"
            :height="100"
            aspect-ratio="1/1"
            class="rounded-md"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { RouterLink } from 'vue-router'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import Badge from '@/components/ui/Badge.vue'
import StarRating from '@/components/ui/StarRating.vue'
import EBImage from '@/components/media/EBImage.vue'
import Loader from '@/components/ui/Loader.vue'
import { fetchMyReviews } from '@/lib/api'
import type { Review } from '@/types/business'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/traveler/header.png'

const reviews = ref<Review[]>([])
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  try {
    const res = await fetchMyReviews()
    reviews.value = res.data
  } finally {
    loading.value = false
  }
})

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

function statusVariant(status: string) {
  switch (status) {
    case 'approved': return 'success'
    case 'rejected': return 'error'
    default: return 'warning'
  }
}
</script>
