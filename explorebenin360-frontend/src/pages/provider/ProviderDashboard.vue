<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Prestataire" :title="t('provider.dashboard')" class="mb-6" />

    <div class="flex items-center justify-between mb-4">
      <div class="text-sm text-[color:var(--color-text-muted)]">{{ t('provider.timespan') }}</div>
      <div class="flex gap-2">
        <button @click="span=7" :class="btnSpan(7)" class="btn-base focus-ring h-8 px-3 rounded-md">7</button>
        <button @click="span=30" :class="btnSpan(30)" class="btn-base focus-ring h-8 px-3 rounded-md">30</button>
        <button @click="span=90" :class="btnSpan(90)" class="btn-base focus-ring h-8 px-3 rounded-md">90</button>
      </div>
    </div>

    <div class="grid md:grid-cols-4 gap-3 mb-6" v-if="!loading">
      <div v-for="k in kpis" :key="k.label" class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ t('provider.summary.' + k.label) }}</div>
        <div class="text-xl font-semibold">{{ formatNumber(k.value) }} <span v-if="k.suffix">{{ k.suffix }}</span></div>
      </div>
    </div>

    <div class="rounded-md border border-black/10 dark:border-white/10 p-4 mb-6">
      <div class="text-sm font-medium mb-2">{{ t('provider.bookings_over_time') }}</div>
      <SmallAreaChart :data="series" color="#06b6d4" />
    </div>

    <div class="grid md:grid-cols-3 gap-3">
      <RouterLink to="/provider/offers" class="rounded-md border border-black/10 dark:border-white/10 p-4 hover:bg-black/5 dark:hover:bg-white/5 focus-ring">{{ t('provider.manage_offers') }}</RouterLink>
      <RouterLink to="/provider/reservations" class="rounded-md border border-black/10 dark:border-white/10 p-4 hover:bg-black/5 dark:hover:bg-white/5 focus-ring">{{ t('provider.view_bookings') }}</RouterLink>
      <RouterLink to="/provider/earnings" class="rounded-md border border-black/10 dark:border-white/10 p-4 hover:bg-black/5 dark:hover:bg-white/5 focus-ring">{{ t('provider.view_earnings') }}</RouterLink>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import SmallAreaChart from '@/components/charts/SmallAreaChart.vue'
import { providerBookingsService } from '@/lib/services/bookings'
import { computeProviderKPIs, buildTimeseries } from '@/lib/services/analytics'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/provider/header.png'
const bookings = ref<any[]>([])
const loading = ref(false)
const kpis = computed(() => computeProviderKPIs(bookings.value))
const span = ref(30)
const series = computed(() => buildTimeseries(span.value))

const formatNumber = (n: number) => new Intl.NumberFormat(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(n)

onMounted(async () => {
  loading.value = true
  try { bookings.value = await providerBookingsService() } finally { loading.value = false }
})

function btnSpan(n: number) {
  return span.value === n ? 'bg-[color:var(--color-secondary)]/20 text-[color:var(--color-secondary)]' : 'border border-black/10 dark:border-white/10'
}
</script>
