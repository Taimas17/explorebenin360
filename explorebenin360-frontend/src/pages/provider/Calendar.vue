<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Calendrier" :title="t('provider.calendar')" class="mb-6" />

    <div class="flex items-center gap-2 mb-4">
      <label class="text-xs text-[color:var(--color-text-muted)]">{{ t('provider.select_offer') }}</label>
      <select v-model.number="selectedId" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
        <option v-for="o in offers" :key="o.id" :value="o.id">{{ o.title }}</option>
      </select>
      <div class="ml-auto flex gap-2">
        <button @click="view='month'" :class="btnView('month')" class="btn-base focus-ring h-8 px-3 rounded-md">{{ t('provider.month') }}</button>
        <button @click="view='week'" :class="btnView('week')" class="btn-base focus-ring h-8 px-3 rounded-md">{{ t('provider.week') }}</button>
      </div>
    </div>

    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else>
      <div v-if="!current" class="text-sm text-[color:var(--color-text-muted)]">{{ t('provider.no_offer_selected') }}</div>
      <div v-else class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-sm text-[color:var(--color-text-muted)] mb-3">{{ t('provider.block_hint') }}</div>
        <div class="grid grid-cols-7 gap-2" v-if="view==='month'">
          <div v-for="d in daysOfMonth" :key="d.iso" class="border border-black/10 dark:border-white/10 rounded-md p-2 min-h-16">
            <div class="text-xs">{{ d.day }}</div>
            <div v-if="isBlocked(d.iso)" class="text-[10px] text-red-600">{{ t('provider.blocked') }}</div>
          </div>
        </div>
        <div v-else class="grid grid-cols-7 gap-2">
          <div v-for="d in daysOfWeek" :key="d.iso" class="border border-black/10 dark:border-white/10 rounded-md p-2 min-h-20">
            <div class="text-xs">{{ d.iso }}</div>
            <div v-if="isBlocked(d.iso)" class="text-[10px] text-red-600">{{ t('provider.blocked') }}</div>
          </div>
        </div>
        <div class="mt-3 flex gap-2">
          <input type="date" v-model="blockDate" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring" />
          <button @click="addBlock" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('provider.block_date') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import { providerOfferings } from '@/lib/services/offerings'
import { updateOfferingAvailability } from '@/lib/api'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/provider/header.png'
const offers = ref<any[]>([])
const selectedId = ref<number | null>(null)
const loading = ref(false)
const view = ref<'month'|'week'>('month')
const blockDate = ref('')

const current = computed(() => offers.value.find(o => o.id === selectedId.value))
const daysOfMonth = computed(() => {
  const now = new Date(); const y = now.getFullYear(); const m = now.getMonth();
  const last = new Date(y, m + 1, 0).getDate();
  return Array.from({ length: last }).map((_, i) => { const d = new Date(y, m, i + 1); return { iso: d.toISOString().slice(0, 10), day: i + 1 } })
})
const daysOfWeek = computed(() => {
  const now = new Date(); const start = new Date(now); start.setDate(now.getDate() - now.getDay());
  return Array.from({ length: 7 }).map((_, i) => { const d = new Date(start); d.setDate(start.getDate() + i); return { iso: d.toISOString().slice(0, 10) } })
})

onMounted(async () => {
  loading.value = true
  try {
    offers.value = await providerOfferings()
    if (offers.value[0]) selectedId.value = offers.value[0].id
  } finally { loading.value = false }
})

function isBlocked(iso: string) {
  const av = (current.value?.availability || current.value?.availability_json) as any || {}
  const blocks: string[] = av.blocks || []
  return blocks.includes(iso)
}

async function addBlock() {
  if (!current.value || !blockDate.value) return
  const av = (current.value.availability || current.value.availability_json) as any || {}
  const blocks: string[] = av.blocks || []
  if (!blocks.includes(blockDate.value)) {
    const next = { ...av, blocks: [...blocks, blockDate.value] }
    current.value.availability = next
    await updateOfferingAvailability(current.value.id, next)
  }
}

function btnView(v: 'month'|'week') { return view.value === v ? 'bg-[color:var(--color-secondary)]/20 text-[color:var(--color-secondary)]' : 'border border-black/10 dark:border-white/10' }
</script>
