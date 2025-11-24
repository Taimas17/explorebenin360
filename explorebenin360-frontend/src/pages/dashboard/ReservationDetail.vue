<template>
  <div class="container-px mx-auto py-8" v-if="item">
    <Alert v-if="$route.query.guard==='access_denied'" type="error" class="mb-4">{{ t('guards.access_denied') }}</Alert>
    <h1 class="text-2xl font-bold mb-2">{{ t('dashboard.reservation_detail') }}</h1>
    <div class="border border-black/10 dark:border-white/10 rounded-md p-4">
      <div class="font-medium mb-1">{{ item.offering.title }}</div>
	      <Alert v-if="$route.query.reason==='login_required'" type="info" class="mb-2">{{ t('guards.login_required') }}</Alert>
      <div class="text-sm text-[color:var(--color-text-muted)] mb-2">{{ item.start_date }}<template v-if="item.end_date"> - {{ item.end_date }}</template> • {{ item.guests }} {{ t('checkout.guests') }}</div>
      <div class="text-sm mb-2">{{ t('dashboard.amount') }}: {{ item.currency }} {{ item.amount }}</div>
      <div class="text-sm mb-4 flex items-center gap-2">{{ t('dashboard.status') }}: <StatusBadge :status="item.status" /></div>
      <div class="flex gap-2">
        <a v-if="item.receipt_url" :href="item.receipt_url" target="_blank" rel="noopener" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.view_receipt') }}</a>
        <button v-if="item.status==='pending' || item.status==='authorized'" @click="cancel" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.cancel') }}</button>
      </div>
    </div>

    <div v-if="item.status==='confirmed'" class="mt-6">
      <h2 class="text-lg font-semibold mb-2">Laisser un avis</h2>
      <ReviewForm :reviewable-type="'App\\\\Models\\\\Offering'" :reviewable-id="item.offering.id" :booking-id="item.id" @success="onReviewSuccess" />
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { fetchBookingService, cancelBookingService } from '@/lib/services/bookings'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import Alert from '@/components/ui/Alert.vue'
import ReviewForm from '@/components/reviews/ReviewForm.vue'

const { t } = useI18n()
const route = useRoute()
const item = ref<any>(null)

onMounted(async () => {
  item.value = await fetchBookingService(Number(route.params.id))
})

function onReviewSuccess() {
  // No-op; could reload booking if needed
}

const cancel = async () => {
  if (!item.value) return
  await cancelBookingService(item.value.id)
  item.value = await fetchBookingService(item.value.id)
}
</script>
