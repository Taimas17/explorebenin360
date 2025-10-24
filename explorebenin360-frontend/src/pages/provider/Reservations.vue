<template>
  <div class="container-px mx-auto py-8">
    <Alert v-if="$route.query.guard==='access_denied'" type="error" class="mb-4">{{ t('guards.access_denied') }}</Alert>
    <BrandBanner :src="banner" alt="" :title="t('provider.reservations')" class="mb-6" />

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

    <div class="flex items-center gap-2 mb-4">
      <Alert v-if="$route.query.reason==='login_required'" type="info" class="mr-2">{{ t('guards.login_required') }}</Alert>
      <label class="text-xs text-[color:var(--color-text-muted)]">{{ t('filters.status') }}</label>
      <select v-model="status" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
        <option value="">{{ t('filters.all') }}</option>
        <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
      </select>
      <label class="text-xs text-[color:var(--color-text-muted)] ml-3">{{ t('filters.date_from') }}</label>
      <input type="date" v-model="from" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring" />
      <label class="text-xs text-[color:var(--color-text-muted)] ml-3">{{ t('filters.date_to') }}</label>
      <input type="date" v-model="to" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring" />
      <button @click="load()" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('filters.apply') }}</button>
    </div>

    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else>
      <EmptyState v-if="items.length === 0" variant="bookings" :title="t('provider.no_reservations')" />
      <div v-else class="space-y-3">
        <div v-for="b in items" :key="b.id" class="border border-black/10 dark:border-white/10 rounded-md p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="font-medium">{{ b.offering.title }}</div>
              <div class="text-xs text-[color:var(--color-text-muted)]">#{{ b.id }} • {{ b.start_date }}<template v-if="b.end_date"> - {{ b.end_date }}</template> • {{ b.guests }} {{ t('checkout.guests') }}</div>
              <div class="text-xs mb-1 flex items-center gap-2">{{ t('dashboard.status') }}: <StatusBadge :status="b.status" /></div>
              <div class="text-xs">{{ t('dashboard.amount') }}: {{ b.currency }} {{ b.amount }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
              <button v-if="b.status==='pending'" @click="accept(b.id)" class="btn-base focus-ring h-8 px-3 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('provider.accept') }}</button>
              <button v-if="b.status==='pending'" @click="refuse(b.id)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('provider.refuse') }}</button>
              <button v-if="b.status==='authorized'" @click="cancel(b.id)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('provider.cancel') }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { providerBookingsService, providerUpdateBookingStatus } from '@/lib/services/bookings'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import Alert from '@/components/ui/Alert.vue'
import providerHeader from '@/assets/brand/images/dashboard/provider/header.png'

const { t } = useI18n()
const items = ref([])
const loading = ref(false)
const currency = 'XOF'
const totals = ref({ total: 0, confirmed: 0, gross: 0, commission: 0, net: 0 })
const banner = providerHeader
const statuses = ['pending','authorized','confirmed','cancelled','refunded']
const status = ref('')
const from = ref('')
const to = ref('')

const formatNumber = (n) => new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n)

onMounted(load)

async function load() {
  loading.value = true
  try {
    items.value = await providerBookingsService({ status: status.value || undefined, from: from.value || undefined, to: to.value || undefined })
    computeTotals()
  } finally { loading.value = false }
}

async function accept(id) {
  const i = items.value.findIndex(b => b.id === id); if (i<0) return
  const prev = { ...items.value[i] }; items.value[i] = { ...items.value[i], status: 'authorized' }
  try { await providerUpdateBookingStatus(id, 'authorized') } catch (e) { items.value[i] = prev }
}
async function refuse(id) {
  const i = items.value.findIndex(b => b.id === id); if (i<0) return
  const prev = { ...items.value[i] }; items.value[i] = { ...items.value[i], status: 'cancelled' }
  try { await providerUpdateBookingStatus(id, 'cancelled') } catch (e) { items.value[i] = prev }
}
async function cancel(id) { return refuse(id) }

function computeTotals() {
  const all = items.value
  const confirmed = all.filter(b => b.status === 'confirmed')
  const gross = confirmed.reduce((sum, b) => sum + Number(b.amount || 0), 0)
  const commission = confirmed.reduce((sum, b) => sum + Number(b.commission_amount || 0), 0)
  const net = gross - commission
  totals.value = { total: all.length, confirmed: confirmed.length, gross, commission, net }
}
</script>
