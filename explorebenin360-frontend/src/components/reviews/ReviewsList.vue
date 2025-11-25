<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchReviews } from '@/lib/services/reviews'
import type { Review } from '@/types/business'
import ReviewCard from './ReviewCard.vue'
import Loader from '@/components/ui/Loader.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const props = defineProps<{
  reviewableType: 'accommodations' | 'guides' | 'places'
  reviewableId: number
}>()

const reviews = ref<Review[]>([])
const loading = ref(false)
const page = ref(1)
const hasMore = ref(true)

async function loadReviews() {
  loading.value = true
  try {
    const { data, meta } = await fetchReviews({
      reviewable_type: props.reviewableType,
      reviewable_id: props.reviewableId,
      per_page: 10,
    })
    reviews.value = data
    hasMore.value = meta.current_page < meta.last_page
  } catch (e) {
    console.error('Failed to load reviews:', e)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadReviews()
})
</script>

<template>
  <div>
    <h3 class="text-lg font-semibold mb-4">Avis des voyageurs</h3>

    <Loader v-if="loading && reviews.length === 0" />

    <EmptyState v-else-if="!loading && reviews.length === 0" title="Aucun avis" description="Soyez le premier à laisser un avis !" />

    <div v-else class="space-y-3">
      <ReviewCard v-for="review in reviews" :key="review.id" :review="review" />
      <div v-if="hasMore" class="text-center mt-4">
        <button @click="loadReviews" :disabled="loading" class="btn-base h-9 px-4 rounded-md border border-black/10 dark:border-white/10">
          {{ loading ? 'Chargement...' : 'Voir plus' }}
        </button>
      </div>
    </div>
  </div>
</template>
