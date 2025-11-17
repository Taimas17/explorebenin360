<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { fetchProviderAnalytics } from '@/lib/api'
import SmallAreaChart from '@/components/charts/SmallAreaChart.vue'
import Loader from '@/components/ui/Loader.vue'
import Card from '@/components/ui/Card.vue'

const { t } = useI18n()

const stats = ref<any>(null)
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  try {
    const res: any = await fetchProviderAnalytics()
    stats.value = res.data
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="container-px mx-auto py-8">
    <div v-if="loading" class="text-center py-8">
      <Loader />
    </div>
    
    <div v-else-if="stats" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <Card>
        <template #title>{{ t('provider.total_offerings') }}</template>
        <div class="text-3xl font-bold">{{ stats.offerings.total }}</div>
        <div class="text-sm text-[color:var(--color-text-muted)]">
          {{ stats.offerings.published }} {{ t('provider.published') }}
        </div>
      </Card>
      
      <Card>
        <template #title>{{ t('provider.total_bookings') }}</template>
        <div class="text-3xl font-bold">{{ stats.bookings.total }}</div>
        <div class="text-sm text-[color:var(--color-text-muted)]">
          {{ stats.bookings.confirmed }} {{ t('provider.confirmed') }}
        </div>
      </Card>
      
      <Card>
        <template #title>{{ t('provider.revenue') }}</template>
        <div class="text-3xl font-bold">
          {{ stats.revenue.net.toLocaleString() }} {{ stats.revenue.currency }}
        </div>
        <div class="text-sm text-[color:var(--color-text-muted)]">
          {{ t('provider.gross') }}: {{ stats.revenue.gross.toLocaleString() }}
        </div>
      </Card>
    </div>

    <div v-if="stats" class="rounded-md border border-black/10 dark:border-white/10 p-4 mt-6">
      <div class="text-sm font-medium mb-2">{{ t('provider.bookings_over_time') }}</div>
      <SmallAreaChart :data="stats.timeseries || []" color="#06b6d4" />
    </div>
  </div>
</template>
