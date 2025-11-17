<template>
  <div class="border border-black/10 dark:border-white/10 rounded-lg p-4">
    <div class="flex items-start justify-between mb-3">
      <div class="flex items-center gap-3">
        <AvatarFallback :name="review.user.name" size="sm" />
        <div>
          <div class="font-medium text-sm">{{ review.user.name }}</div>
          <div class="text-xs text-[color:var(--color-text-muted)]">
            {{ formatDate(review.created_at) }}
          </div>
        </div>
      </div>
      <StarRating :rating="review.rating" size="sm" readonly />
    </div>
    
    <p class="text-sm mb-3 whitespace-pre-wrap">{{ review.comment }}</p>
    
    <div v-if="review.photos && review.photos.length > 0" class="grid grid-cols-4 gap-2">
      <EBImage
        v-for="(photo, idx) in review.photos"
        :key="idx"
        :src="photo"
        :alt="`Photo ${idx + 1}`"
        :width="200"
        :height="200"
        aspect-ratio="1 / 1"
        class="rounded-md cursor-pointer hover:opacity-80 transition"
        @click="openGallery(idx)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Review } from '@/types/business'
import AvatarFallback from '@/components/ui/AvatarFallback.vue'
import StarRating from '@/components/ui/StarRating.vue'
import EBImage from '@/components/media/EBImage.vue'

const props = defineProps<{ review: Review }>()

function formatDate(date: string): string {
  const d = new Date(date)
  return d.toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

function openGallery(index: number) {
  console.log('Open gallery at', index)
}
</script>
