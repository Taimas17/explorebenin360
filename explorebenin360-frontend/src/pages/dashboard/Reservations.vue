<template>
  <div class="container-px mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">{{ t('dashboard.my_reservations') }}</h1>
    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else>
      <div v-if="items.length === 0" class="text-sm text-[color:var(--color-text-muted)]">{{ t('dashboard.no_reservations') }}</div>
      <div v-else class="space-y-3">
        <div v-for="b in items" :key="b.id" class="border border-black/10 dark:border-white/10 rounded-md p-4 flex justify-between items-center">
          <div>
            <div class="font-medium">{{ b.offering.title }}</div>
            <div class="text-xs text-[color:var(--color-text-muted)]">{{ b.start_date }}<template v-if="b.end_date"> - {{ b.end_date }}</template> • {{ b.guests }} {{ t('checkout.guests') }}</div>
            <div class="text-xs">{{ t('dashboard.status') }}: <span class="font-medium">{{ b.status }}</span></div>
          </div>
          <RouterLink :to="{ name: 'reservation-detail', params: { id: b.id } }" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('common.details') }}</RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '@/lib/api'

const { t } = useI18n()
const items = ref([])
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  try {
    const res = await api.get('/bookings')
    items.value = res.data.data ?? res.data
  } finally { loading.value = false }
})
</script>
