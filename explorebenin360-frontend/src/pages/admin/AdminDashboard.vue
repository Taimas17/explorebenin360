<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchAnalyticsOverview, fetchTimeseries, fetchTopContent, fetchRecentActivity, exportData, downloadFile } from '@/lib/services/admin-analytics'
import type { AnalyticsOverview, TimeseriesData } from '@/lib/services/admin-analytics'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import Card from '@/components/ui/Card.vue'
import SmallAreaChart from '@/components/charts/SmallAreaChart.vue'
import Loader from '@/components/ui/Loader.vue'
import Alert from '@/components/ui/Alert.vue'

const overview = ref<AnalyticsOverview | null>(null)
const bookingsTimeseries = ref<TimeseriesData | null>(null)
const revenueTimeseries = ref<TimeseriesData | null>(null)
const topAccommodations = ref<any[]>([])
const recentActivity = ref<any[]>([])

const loading = ref(false)
const error = ref<string | null>(null)

const selectedPeriod = ref('30d')

const periodOptions = [
  { value: '7d', label: '7 jours' },
  { value: '30d', label: '30 jours' },
  { value: '90d', label: '90 jours' },
  { value: '1y', label: '1 an' },
]

async function loadDashboard() {
  loading.value = true
  error.value = null
  
  try {
    const [overviewData, bookingsSeries, revenueSeries, topContent, activity] = await Promise.all([
      fetchAnalyticsOverview({ period: selectedPeriod.value }),
      fetchTimeseries({ metric: 'bookings', period: selectedPeriod.value, granularity: 'day' }),
      fetchTimeseries({ metric: 'revenue', period: selectedPeriod.value, granularity: 'day' }),
      fetchTopContent({ type: 'accommodations', metric: 'bookings', limit: 10, period: selectedPeriod.value }),
      fetchRecentActivity(10),
    ])
    
    overview.value = overviewData
    bookingsTimeseries.value = bookingsSeries
    revenueTimeseries.value = revenueSeries
    topAccommodations.value = topContent.items
    recentActivity.value = activity
  } catch (e: any) {
    error.value = e.message || 'Erreur de chargement'
  } finally {
    loading.value = false
  }
}

async function handleExport(type: string) {
  try {
    const blob = await exportData({ type: type as any, format: 'csv', period: selectedPeriod.value })
    const filename = `${type}_export_${new Date().toISOString().split('T')[0]}.csv`
    downloadFile(blob, filename)
  } catch (e: any) {
    alert('Erreur lors de l\'export : ' + e.message)
  }
}

onMounted(() => {
  loadDashboard()
})
</script>

<template>
  <BrandBanner title="Dashboard Admin" />
  
  <div class="container-px py-8">
    <div class="flex justify-between items-center mb-6">
      <div>
        <label class="mr-2">Période:</label>
        <select v-model="selectedPeriod" @change="loadDashboard" class="border rounded px-3 py-1">
          <option v-for="opt in periodOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
        </select>
      </div>
      
      <div class="space-x-2">
        <button @click="handleExport('users')" class="btn-base">Export Users</button>
        <button @click="handleExport('bookings')" class="btn-base">Export Bookings</button>
        <button @click="handleExport('revenue')" class="btn-base">Export Revenue</button>
      </div>
    </div>
    
    <Loader v-if="loading" />
    <Alert v-else-if="error" type="error">{{ error }}</Alert>
    
    <div v-else-if="overview">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <Card title="Utilisateurs">
          <div class="text-3xl font-bold">{{ overview.kpis.users.total.toLocaleString() }}</div>
          <div class="text-sm text-muted">{{ overview.kpis.users.new }} nouveaux</div>
          <div class="text-sm" :class="overview.kpis.users.growth >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ overview.kpis.users.growth > 0 ? '+' : '' }}{{ overview.kpis.users.growth }}%
          </div>
        </Card>
        
        <Card title="Réservations">
          <div class="text-3xl font-bold">{{ overview.kpis.bookings.total.toLocaleString() }}</div>
          <div class="text-sm text-muted">{{ overview.kpis.bookings.confirmed }} confirmées</div>
          <div class="text-sm text-green-600">{{ overview.kpis.bookings.conversion_rate }}% conversion</div>
        </Card>
        
        <Card title="Revenue">
          <div class="text-3xl font-bold">{{ overview.kpis.revenue.currency }} {{ overview.kpis.revenue.total.toLocaleString() }}</div>
          <div class="text-sm text-muted">Commission: {{ overview.kpis.revenue.commission.toLocaleString() }}</div>
          <div class="text-sm text-muted">Moyenne: {{ overview.kpis.revenue.average.toLocaleString() }}</div>
        </Card>
        
        <Card title="Providers">
          <div class="text-3xl font-bold">{{ overview.kpis.providers.total.toLocaleString() }}</div>
          <div class="text-sm text-muted">{{ overview.kpis.providers.pending }} en attente</div>
          <div class="text-sm text-green-600">{{ overview.kpis.providers.active }} actifs</div>
        </Card>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <Card title="Réservations">
          <SmallAreaChart v-if="bookingsTimeseries" :data="bookingsTimeseries.series.map(s => ({ date: s.date, value: s.value }))" color="#3b82f6" />
        </Card>
        
        <Card title="Revenue">
          <SmallAreaChart v-if="revenueTimeseries" :data="revenueTimeseries.series.map(s => ({ date: s.date, value: s.value }))" color="#10b981" />
        </Card>
      </div>
      
      <div class="mb-8">
        <Card title="Top Accommodations (par réservations)">
          <ul class="space-y-2">
            <li v-for="item in topAccommodations" :key="item.id" class="flex justify-between items-center border-b pb-2">
              <span>{{ item.title }}</span>
              <span class="font-semibold">{{ item.metric_value }} bookings</span>
            </li>
          </ul>
        </Card>
      </div>
      
      <Card title="Activités récentes">
        <ul class="space-y-2">
          <li v-for="act in recentActivity" :key="act.id" class="text-sm">
            {{ act.description }} <span class="text-muted">- {{ new Date(act.created_at).toLocaleString() }}</span>
          </li>
        </ul>
      </Card>
    </div>
  </div>
</template>
