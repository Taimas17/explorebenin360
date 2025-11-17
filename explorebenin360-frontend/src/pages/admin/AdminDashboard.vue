<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Admin" :title="t('admin.dashboard')" class="mb-6" />

    <div v-if="loading" class="text-center py-8">
      <Loader />
    </div>

    <div v-else>
      <!-- KPIs -->
      <div class="grid md:grid-cols-4 gap-3 mb-6">
        <Card>
          <template #title>{{ t('admin.kpi_users') }}</template>
          <div class="text-2xl font-bold">{{ kpis.users.total.toLocaleString() }}</div>
          <div class="text-xs text-green-600">
            +{{ kpis.users.new_this_month }} {{ t('admin.this_month') }}
          </div>
        </Card>
        
        <Card>
          <template #title>{{ t('admin.kpi_providers_pending') }}</template>
          <div class="text-2xl font-bold">{{ kpis.providers.pending }}</div>
          <div class="text-xs text-[color:var(--color-text-muted)]">
            {{ kpis.providers.total }} {{ t('admin.total_approved') }}
          </div>
        </Card>
        
        <Card>
          <template #title>{{ t('admin.kpi_bookings') }}</template>
          <div class="text-2xl font-bold">{{ kpis.bookings.total.toLocaleString() }}</div>
          <div class="text-xs text-[color:var(--color-text-muted)]">
            {{ kpis.bookings.confirmed }} {{ t('admin.confirmed') }}
          </div>
        </Card>
        
        <Card>
          <template #title>{{ t('admin.kpi_revenue') }}</template>
          <div class="text-2xl font-bold">
            {{ kpis.revenue.total.toLocaleString() }} {{ kpis.revenue.currency }}
          </div>
          <div class="text-xs text-green-600">
            {{ kpis.revenue.this_month.toLocaleString() }} {{ t('admin.this_month') }}
          </div>
        </Card>
      </div>

      <!-- Statistiques de conversion et contenu -->
      <div class="grid md:grid-cols-2 gap-3 mb-6">
        <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
          <div class="text-sm font-medium mb-2">{{ t('admin.conversion_stats') }}</div>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span>{{ t('admin.booking_conversion') }}</span>
              <span class="font-semibold">{{ conversionStats.booking_conversion_rate }}%</span>
            </div>
            <div class="flex justify-between">
              <span>{{ t('admin.offering_utilization') }}</span>
              <span class="font-semibold">{{ conversionStats.offering_utilization_rate }}%</span>
            </div>
            <div class="flex justify-between">
              <span>{{ t('admin.cancellation_rate') }}</span>
              <span class="font-semibold">{{ Number(conversionStats.cancellation_rate || 0).toFixed(1) }}%</span>
            </div>
          </div>
        </div>
        
        <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
          <div class="text-sm font-medium mb-2">{{ t('admin.content_stats') }}</div>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span>{{ t('admin.places') }}</span>
              <span class="font-semibold">{{ kpis.content.places }}</span>
            </div>
            <div class="flex justify-between">
              <span>{{ t('admin.offerings') }}</span>
              <span class="font-semibold">{{ kpis.content.offerings }}</span>
            </div>
            <div class="flex justify-between">
              <span>{{ t('admin.articles') }}</span>
              <span class="font-semibold">{{ kpis.content.articles }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Chart -->
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4 mb-6">
        <div class="flex items-center justify-between mb-3">
          <div class="text-sm font-medium">{{ t('admin.bookings_over_time') }}</div>
          <select v-model="chartDays" @change="loadTimeseries" class="text-sm rounded border border-black/10 dark:border-white/10 px-2 py-1">
            <option :value="7">7 jours</option>
            <option :value="30">30 jours</option>
            <option :value="90">90 jours</option>
          </select>
        </div>
        <SmallAreaChart :data="timeseries" color="#16a34a" />
      </div>

      <!-- Activités récentes -->
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-sm font-medium mb-3">{{ t('admin.recent_activity') }}</div>
        <ul class="space-y-2">
          <li
            v-for="activity in recentActivity"
            :key="activity.id"
            class="text-sm flex items-start justify-between py-2 border-b border-black/10 dark:border-white/10 last:border-0"
          >
            <span>{{ activity.text }}</span>
            <StatusBadge v-if="activity.status" :status="activity.status" />
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import Card from '@/components/ui/Card.vue'
import Loader from '@/components/ui/Loader.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import SmallAreaChart from '@/components/charts/SmallAreaChart.vue'
import { 
  fetchAdminKPIs, 
  fetchBookingsTimeseries, 
  fetchRecentActivity,
  fetchConversionStats 
} from '@/lib/api'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'

const loading = ref(false)
const kpis = ref<any>({
  users: { total: 0, new_this_month: 0 },
  providers: { total: 0, pending: 0 },
  content: { places: 0, offerings: 0, articles: 0 },
  bookings: { total: 0, confirmed: 0, pending: 0 },
  revenue: { total: 0, commission: 0, this_month: 0, currency: 'XOF' },
})
const conversionStats = ref<any>({
  booking_conversion_rate: 0,
  offering_utilization_rate: 0,
  average_booking_value: 0,
  cancellation_rate: 0,
})
const timeseries = ref<any[]>([])
const recentActivity = ref<any[]>([])
const chartDays = ref(30)

onMounted(async () => {
  await loadDashboard()
})

async function loadDashboard() {
  loading.value = true
  try {
    const [kpisRes, conversionRes, activityRes] = await Promise.all([
      fetchAdminKPIs(),
      fetchConversionStats(),
      fetchRecentActivity(20),
    ])
    
    kpis.value = (kpisRes as any).data
    conversionStats.value = (conversionRes as any).data
    recentActivity.value = (activityRes as any).data
    
    await loadTimeseries()
  } finally {
    loading.value = false
  }
}

async function loadTimeseries() {
  const res = await fetchBookingsTimeseries(chartDays.value)
  timeseries.value = (res as any).data
}
</script>
