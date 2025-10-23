<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Admin" :title="t('admin.reservations')" class="mb-6" />
    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else>
      <EmptyState v-if="items.length === 0" variant="bookings" :title="t('admin.no_reservations')" />
      <div v-else class="space-y-3">
        <div v-for="b in items" :key="b.id" class="border border-black/10 dark:border-white/10 rounded-md p-4">
          <div class="flex justify-between items-center">
            <div>
              <div class="font-medium">#{{ b.id }} • {{ b.offering.title }}</div>
              <div class="text-xs text-[color:var(--color-text-muted)]">{{ b.user?.name }} • {{ b.start_date }}<template v-if="b.end_date"> - {{ b.end_date }}</template></div>
              <div class="text-xs">{{ t('dashboard.status') }}: <span class="font-medium">{{ b.status }}</span></div>
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
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { adminBookings, adminUpdateBooking } from '@/lib/api'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import adminHeader from '@/assets/brand/images/dashboard/admin/header.png'

const { t } = useI18n()
const items = ref([])
const loading = ref(false)
const statuses = ['pending','authorized','confirmed','cancelled','refunded']
const selected = ref({})
const saving = ref({})
const banner = adminHeader

onMounted(async () => {
  loading.value = true
  try {
    const res = await adminBookings();
    items.value = res.data.data ?? res.data
    const map = {}
    items.value.forEach((b) => { map[b.id] = b.status })
    selected.value = map
  } finally { loading.value = false }
})

const updateStatus = async (id) => {
  try {
    saving.value[id] = true
    const status = selected.value[id]
    await adminUpdateBooking(id, { status })
    const item = items.value.find(b => b.id === id)
    if (item) item.status = status
    alert(t('admin.updated_success'))
  } catch (e) {
    alert('Update failed')
  } finally {
    saving.value[id] = false
  }
}
</script>
