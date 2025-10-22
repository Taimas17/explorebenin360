<template>
  <div class="container-px mx-auto py-8">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">{{ t('provider.reservations') }}</h1>
    </div>

    <div class="grid md:grid-cols-4 gap-3 mb-6" v-if="!loading">
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ t('provider.summary.total_bookings') }}</div>
        <div class="text-xl font-semibold">{{ totals.total }}</div>
      </div>
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ t('provider.summary.confirmed_bookings') }}</div>
        <div class="text-xl font-semibold">{{ totals.confirmed }}</div>
      </div>
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ t('provider.summary.gross_amount') }}</div>
        <div class="text-xl font-semibold">{{ currency }} {{ formatNumber(totals.gross) }}</div>
      </div>
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ t('provider.summary.net_earnings') }}</div>
        <div class="text-xl font-semibold">{{ currency }} {{ formatNumber(totals.net) }}</div>
        <div class="text-[10px] text-[color:var(--color-text-muted)]">{{ t('provider.summary.commission_total') }}: {{ currency }} {{ formatNumber(totals.commission) }}</div>
      </div>
    </div>

    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else>
      <div v-if="items.length === 0" class="text-sm text-[color:var(--color-text-muted)]">{{ t('provider.no_reservations') }}</div>
      <div v-else class="space-y-3">
        <div v-for="b in items" :key="b.id" class="border border-black/10 dark:border-white/10 rounded-md p-4">
          <div class="font-medium">{{ b.offering.title }}</div>
          <div class="text-xs text-[color:var(--color-text-muted)]">{{ b.start_date }}<template v-if="b.end_date"> - {{ b.end_date }}</template> • {{ b.guests }} {{ t('checkout.guests') }}</div>
          <div class="text-xs mb-1">{{ t('dashboard.status') }}: <span class="font-medium">{{ b.status }}</span></div>
          <div class="text-xs">{{ t('dashboard.amount') }}: {{ b.currency }} {{ b.amount }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { providerBookings } from '@/lib/api'

const { t } = useI18n()
const items = ref([])
const loading = ref(false)
const currency = 'XOF'
const totals = ref({ total: 0, confirmed: 0, gross: 0, commission: 0, net: 0 })

const formatNumber = (n) => new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n)

onMounted(async () => {
  loading.value = true
  try {
    const res = await providerBookings();
    items.value = res.data.data ?? res.data
    computeTotals()
  } finally { loading.value = false }
})

const computeTotals = () => {
  const all = items.value
  const confirmed = all.filter(b => b.status === 'confirmed')
  const gross = confirmed.reduce((sum, b) => sum + Number(b.amount || 0), 0)
  const commission = confirmed.reduce((sum, b) => sum + Number(b.commission_amount || 0), 0)
  const net = gross - commission
  totals.value = { total: all.length, confirmed: confirmed.length, gross, commission, net }
}
</script>
