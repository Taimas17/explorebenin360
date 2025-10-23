<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Gains" :title="t('provider.earnings')" class="mb-6" />

    <div class="flex items-center justify-between mb-3">
      <div class="text-sm text-[color:var(--color-text-muted)]">{{ t('provider.payouts_desc') }}</div>
      <button @click="exportCsv" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('provider.export_csv') }}</button>
    </div>

    <div class="rounded-md border border-black/10 dark:border-white/10 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-black/5 dark:bg-white/5">
          <tr>
            <th class="text-left px-3 py-2">#</th>
            <th class="text-left px-3 py-2">{{ t('provider.date') }}</th>
            <th class="text-left px-3 py-2">{{ t('provider.amount') }}</th>
            <th class="text-left px-3 py-2">{{ t('provider.status') }}</th>
            <th class="text-left px-3 py-2">{{ t('provider.reference') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in payouts" :key="p.id" class="border-t border-black/10 dark:border-white/10">
            <td class="px-3 py-2">{{ p.id }}</td>
            <td class="px-3 py-2">{{ p.date }}</td>
            <td class="px-3 py-2">{{ p.currency }} {{ p.amount }}</td>
            <td class="px-3 py-2">{{ p.status }}</td>
            <td class="px-3 py-2">{{ p.reference }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <EmptyState v-if="payouts.length === 0" variant="default" :title="t('provider.no_payouts')" class="mt-6" />
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/provider/header.png'
const payouts = ref<any[]>([])

onMounted(() => {
  payouts.value = [
    { id: 1, date: new Date().toISOString().slice(0, 10), amount: 120000, currency: 'XOF', status: 'paid', reference: 'TX-2025-0001' },
    { id: 2, date: new Date(Date.now() - 86400000).toISOString().slice(0, 10), amount: 95000, currency: 'XOF', status: 'pending', reference: 'TX-2025-0002' },
  ]
})

function exportCsv() {
  const header = ['id','date','amount','currency','status','reference']
  const rows = payouts.value.map((p: any) => [p.id, p.date, p.amount, p.currency, p.status, p.reference])
  const csv = [header, ...rows].map(r => r.join(',')).join('\n')
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'earnings.csv'
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  URL.revokeObjectURL(url)
}
</script>
