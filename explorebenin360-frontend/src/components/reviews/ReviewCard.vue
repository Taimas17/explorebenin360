<script setup lang="ts">
import type { Review } from '@/types/business'
import StarRating from './StarRating.vue'

defineProps<{
  review: Review
}>()
</script>

<template>
  <div class="border border-black/10 dark:border-white/10 rounded-md p-4">
    <div class="flex items-start gap-3">
      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[color:var(--color-secondary)] to-[color:var(--color-accent)] flex items-center justify-center text-white font-semibold text-sm">
        {{ review.user.name.charAt(0).toUpperCase() }}
      </div>
      <div class="flex-1">
        <div class="flex items-center justify-between mb-2">
          <div>
            <div class="font-medium">{{ review.user.name }}</div>
            <StarRating :rating="review.rating" readonly size="sm" />
          </div>
          <div class="text-xs text-[color:var(--color-text-muted)]">
            {{ new Date(review.created_at).toLocaleDateString() }}
          </div>
        </div>
        <p v-if="review.comment" class="text-sm text-[color:var(--color-text)]">
          {{ review.comment }}
        </p>
      </div>
    </div>
  </div>
</template>
