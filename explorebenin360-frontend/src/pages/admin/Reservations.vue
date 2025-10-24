<template>
  <div class="container-px mx-auto py-8">
    <Alert v-if="$route.query.guard==='access_denied'" type="error" class="mb-4">{{ t('guards.access_denied') }}</Alert>
    <BrandBanner :src="banner" alt="" :title="t('admin.reservations')" class="mb-6" />

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
      <input v-model="q" :placeholder="t('filters.search_placeholder')" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring flex-1" />
      <button @click="load()" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('filters.apply') }}</button>
    </div>

    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else>
      <EmptyState v-if="filtered.length === 0" variant="bookings" :title="t('admin.no_reservations')" />
      <div v-else class="space-y-3">
        <div v-for="b in filtered" :key="b.id" class="border border-black/10 dark:border-white/10 rounded-md p-4">
          <div class="flex justify-between items-center">
            <div>
              <div class="font-medium">#{{ b.id }} • {{ b.offering.title }}</div>
              <div class="text-xs text-[color:var(--color-text-muted)]">{{ b.user?.name }} • {{ b.start_date }}<template v-if="b.end_date"> - {{ b.end_date }}</template></div>
              <div class="text-xs flex items-center gap-2">{{ t('dashboard.status') }}: <StatusBadge :status="b.status" /></div>
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs text-[color:var(--color-text-muted)]">{{ t('admin.change_status') }}</label>
              <select v-model="selected[b.id]" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
              </select>
              <button @click="updateStatus(b.id)" :disabled="saving[b.id]" class="btn-base focus-ring h-9 px-3 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
                {{ saving[b.id] ? t('common.loading') : t('admin.update') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { adminBookingsService, adminUpdateBookingStatus } from '@/lib/services/bookings'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import Alert from '@/components/ui/Alert.vue'
import adminHeader from '@/assets/brand/images/dashboard/admin/header.png'

const { t } = useI18n()
const items = ref([])
const loading = ref(false)
const statuses = ['pending','authorized','confirmed','cancelled','refunded']
const selected = ref<Record<number, string>>({})
const saving = ref<Record<number, boolean>>({})
const banner = adminHeader
const status = ref('')
const from = ref('')
const to = ref('')
const q = ref('')

const filtered = computed(() => {
  const needle = q.value.trim().toLowerCase()
  if (!needle) return items.value
  return items.value.filter((b: any) => {
    return (
      String(b.id).includes(needle) ||
      (b.offering?.title || '').toLowerCase().includes(needle) ||
      (b.user?.name || '').toLowerCase().includes(needle)
    )
  })
})

onMounted(load)

async function load() {
  loading.value = true
  try {
    items.value = await adminBookingsService({ status: status.value || undefined, from: from.value || undefined, to: to.value || undefined })
    const map: Record<number, string> = {}
    items.value.forEach((b: any) => { map[b.id] = b.status })
    selected.value = map
  } finally { loading.value = false }
}

async function updateStatus(id: number) {
  try {
    saving.value[id] = true
    const status = selected.value[id]
    await adminUpdateBookingStatus(id, status as any)
    const item = items.value.find((b: any) => b.id === id)
    if (item) item.status = status
    alert(t('admin.updated_success'))
  } catch (e) {
    alert('Update failed')
  } finally {
    saving.value[id] = false
  }
}
</script>
