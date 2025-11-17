<template>
  <div class="container-px mx-auto py-8" v-if="item">
    <h1 class="text-3xl font-bold mb-2">{{ item.title }}</h1>
    <p class="text-[color:var(--color-text-muted)] mb-4">
      {{ item.type }} • {{ item.currency }} {{ item.price }} • {{ t('offerings.capacity') }} {{ item.capacity }}
    </p>
    <p class="mb-6">{{ item.description }}</p>
    <RouterLink
      :to="{ name: 'checkout', params: { slug: item.slug } }"
      class="inline-flex btn-base focus-ring h-10 px-5 rounded-md text-white"
      :style="{ backgroundColor: 'var(--color-primary)' }"
    >
      {{ t('checkout.book_now') }}
    </RouterLink>

    <section class="mt-12">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">{{ t('reviews.title') }}</h2>
        <div v-if="totalReviews > 0" class="flex items-center gap-2">
          <StarRating :rating="averageRating" readonly showValue />
          <span class="text-sm text-[color:var(--color-text-muted)]">
            ({{ totalReviews }} {{ t('reviews.reviews') }})
          </span>
        </div>
      </div>

      <div v-if="loadingReviews" class="text-center py-8">
        <Loader />
      </div>

      <EmptyState
        v-else-if="reviews.length === 0"
        variant="default"
        :title="t('reviews.no_reviews')"
      />

      <div v-else class="space-y-4">
        <ReviewCard
          v-for="review in reviews"
          :key="review.id"
          :review="review"
        />
        <!-- TODO: Pagination si nécessaire -->
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { fetchOffering, fetchOfferingReviews } from '@/lib/api'
import { setPageMeta } from '@/utils/meta'
import ReviewCard from '@/components/reviews/ReviewCard.vue'
import StarRating from '@/components/ui/StarRating.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import Loader from '@/components/ui/Loader.vue'
import type { Review } from '@/types/business'

const { t } = useI18n()
const route = useRoute()
const item = ref<any>(null)

const reviews = ref<Review[]>([])
const reviewsMeta = ref<Record<string, any>>({})
const loadingReviews = ref(false)

const averageRating = computed(() => {
  if (reviews.value.length === 0) return 0
  const sum = reviews.value.reduce((acc, r) => acc + r.rating, 0)
  return sum / reviews.value.length
})

const totalReviews = computed(() => reviewsMeta.value?.total ?? reviews.value.length ?? 0)

async function loadReviews(offeringId: number) {
  loadingReviews.value = true
  try {
    const res = await fetchOfferingReviews(offeringId, { per_page: 10 })
    reviews.value = res.data
    reviewsMeta.value = res.meta
  } finally {
    loadingReviews.value = false
  }
}

onMounted(async () => {
  const slug = route.params.slug?.toString() || ''
  const res = await fetchOffering(slug)
  item.value = res.data
  setPageMeta({
    title: `${item.value.title} — ExploreBenin360`,
    description: (item.value.description || '').slice(0, 160),
    path: `/offerings/${item.value.slug}`
  })
  await loadReviews(item.value.id)
})
</script>
