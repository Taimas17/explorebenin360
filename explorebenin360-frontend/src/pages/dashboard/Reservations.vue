<template>
  <div class="container-px mx-auto py-8">
    <Alert v-if="$route.query.guard==='access_denied'" type="error" class="mb-4">{{ t('guards.access_denied') }}</Alert>
    <BrandBanner :src="banner" alt="" :title="t('dashboard.my_reservations')" class="mb-6" />

    <div class="flex items-center justify-between mb-4">
	    <Alert v-if="$route.query.reason==='login_required'" type="info" class="mr-2">{{ t('guards.login_required') }}</Alert>
      <div class="flex gap-2 text-sm">
        <button @click="filter='upcoming'" :class="tabClass('upcoming')" class="px-3 py-1 rounded-md focus-ring">{{ t('dashboard.upcoming') }}</button>
        <button @click="filter='past'" :class="tabClass('past')" class="px-3 py-1 rounded-md focus-ring">{{ t('dashboard.past') }}</button>
        <button @click="filter='all'" :class="tabClass('all')" class="px-3 py-1 rounded-md focus-ring">{{ t('dashboard.all') }}</button>
      </div>
      <div class="flex gap-2">
        <RouterLink to="/dashboard/favorites" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.favorites') }}</RouterLink>
        <RouterLink to="/dashboard/messages" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.messages') }}</RouterLink>
      </div>
    </div>

    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else>
      <EmptyState v-if="filtered.length === 0" variant="bookings" :title="t('dashboard.no_reservations')">
        <RouterLink to="/offerings" class="btn-base focus-ring h-9 px-3 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('hero.cta') }}</RouterLink>
      </EmptyState>
      <div v-else class="space-y-3">
        <div v-for="b in filtered" :key="b.id" class="border border-black/10 dark:border-white/10 rounded-md p-4 flex flex-wrap gap-3 items-center justify-between">
          <div>
            <div class="font-medium">{{ b.offering.title }}</div>
            <div class="text-xs text-[color:var(--color-text-muted)]">{{ b.start_date }}<template v-if="b.end_date"> - {{ b.end_date }}</template> • {{ b.guests }} {{ t('checkout.guests') }}</div>
            <div class="text-xs flex items-center gap-2">{{ t('dashboard.status') }}: <StatusBadge :status="b.status" /></div>
          </div>
          <div class="flex items-center gap-2">
            <button
              v-if="b.status === 'confirmed' && !b.has_review"
              @click="openReviewModal(b)"
              class="btn-base focus-ring h-9 px-3 rounded-md text-white"
              :style="{ backgroundColor: 'var(--color-secondary)' }"
            >
              {{ t('reviews.leave_review') }}
            </button>
            <a v-if="b.receipt_url" :href="b.receipt_url" target="_blank" rel="noopener" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.view_receipt') }}</a>
            <RouterLink :to="{ name: 'reservation-detail', params: { id: b.id } }" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('common.details') }}</RouterLink>
          </div>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <div
        v-if="showReviewModal"
        class="fixed inset-0 bg-black/50 z-50 grid place-items-center p-4"
        @click.self="showReviewModal = false"
      >
        <div class="bg-white dark:bg-gray-900 rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <h2 class="text-xl font-bold mb-4">{{ t('reviews.leave_review_for') }} {{ selectedBooking?.offering.title }}</h2>
          <ReviewForm
            v-if="selectedBooking"
            :booking-id="selectedBooking.id"
            @success="handleReviewSuccess"
            @cancel="showReviewModal = false"
          />
        </div>
      </div>
    </Teleport>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import Alert from '@/components/ui/Alert.vue'
import ReviewForm from '@/components/reviews/ReviewForm.vue'
import { travelerBookings } from '@/lib/services/bookings'
import travelerHeader from '@/assets/brand/images/dashboard/traveler/header.png'
import type { Booking } from '@/types/business'

type BookingWithReview = Booking & { has_review?: boolean }

const { t } = useI18n()
const items = ref<BookingWithReview[]>([])
const loading = ref(false)
const banner = travelerHeader
const filter = ref<'all'|'upcoming'|'past'>('all')
const showReviewModal = ref(false)
const selectedBooking = ref<BookingWithReview | null>(null)

const filtered = computed(() => {
  const today = new Date().toISOString().slice(0, 10)
  if (filter.value === 'all') return items.value
  if (filter.value === 'upcoming') return items.value.filter((b) => (b.end_date || b.start_date) >= today)
  return items.value.filter((b) => (b.end_date || b.start_date) < today)
})

function tabClass(key: string) {
  return filter.value === key ? 'bg-[color:var(--color-secondary)]/20 text-[color:var(--color-secondary)]' : 'border border-black/10 dark:border-white/10'
}

async function fetchBookings() {
  loading.value = true
  try {
    items.value = (await travelerBookings()) as BookingWithReview[]
  } finally {
    loading.value = false
  }
}

function openReviewModal(booking: BookingWithReview) {
  selectedBooking.value = booking
  showReviewModal.value = true
}

async function handleReviewSuccess() {
  showReviewModal.value = false
  alert(t('reviews.submitted_success'))
  await fetchBookings()
}

onMounted(fetchBookings)
</script>
