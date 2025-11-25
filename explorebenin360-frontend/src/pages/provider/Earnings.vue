<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchBalance, fetchPayouts, createPayout } from '@/lib/services/payouts'
import type { Balance, Payout } from '@/types/business'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import Loader from '@/components/ui/Loader.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'

const balance = ref<Balance | null>(null)
const payouts = ref<Payout[]>([])
const loading = ref(false)
const showRequestModal = ref(false)
const requestAmount = ref(0)

async function load() {
  loading.value = true
  try {
    const [balanceData, payoutsData] = await Promise.all([
      fetchBalance(),
      fetchPayouts({ per_page: 50 })
    ])
    balance.value = balanceData
    payouts.value = payoutsData.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function requestPayout() {
  if (requestAmount.value < 1000) {
    alert('Montant minimum: 1000 XOF')
    return
  }
  if (requestAmount.value > (balance.value?.available_balance ?? 0)) {
    alert('Solde insuffisant')
    return
  }
  try {
    await createPayout({ amount: requestAmount.value })
    showRequestModal.value = false
    requestAmount.value = 0
    load()
  } catch (e: any) {
    alert(e?.response?.data?.message || 'Erreur')
  }
}

function exportCSV() {
  const header = ['reference','amount','currency','status','requested_at','completed_at']
  const rows = payouts.value.map((p: any) => [p.reference, p.amount, p.currency, p.status, p.requested_at, p.completed_at || ''])
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

onMounted(() => {
  load()
})
</script>

<template>
  <BrandBanner title="Mes gains" />
  
  <div class="container-px py-8">
    <Loader v-if="loading" />
    
    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="border border-black/10 dark:border-white/10 rounded-lg p-4">
          <div class="text-xs text-[color:var(--color-text-muted)] mb-1">Gains totaux</div>
          <div class="text-2xl font-bold">{{ balance?.currency }} {{ balance?.total_earnings.toLocaleString() }}</div>
        </div>
        
        <div class="border border-black/10 dark:border-white/10 rounded-lg p-4">
          <div class="text-xs text-[color:var(--color-text-muted)] mb-1">Retraits effectués</div>
          <div class="text-2xl font-bold">{{ balance?.currency }} {{ balance?.total_payouts.toLocaleString() }}</div>
        </div>
        
        <div class="border border-black/10 dark:border-white/10 rounded-lg p-4">
          <div class="text-xs text-[color:var(--color-text-muted)] mb-1">En attente</div>
          <div class="text-2xl font-bold">{{ balance?.currency }} {{ balance?.pending_payouts.toLocaleString() }}</div>
        </div>
        
        <div class="border border-black/10 dark:border-white/10 rounded-lg p-4 bg-green-500/10">
          <div class="text-xs text-[color:var(--color-text-muted)] mb-1">Solde disponible</div>
          <div class="text-2xl font-bold text-green-600">{{ balance?.currency }} {{ balance?.available_balance.toLocaleString() }}</div>
          <button @click="showRequestModal = true" :disabled="(balance?.available_balance ?? 0) < 1000"
                  class="btn-base h-8 px-3 rounded-md text-xs text-white bg-green-600 mt-2">
            Demander un retrait
          </button>
        </div>
      </div>
      
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Historique des retraits</h3>
        <button @click="exportCSV" class="btn-base h-9 px-4 rounded-md border border-black/10 dark:border-white/10">
          Exporter CSV
        </button>
      </div>
      
      <table class="min-w-full text-sm">
        <thead class="bg-black/5 dark:bg-white/5">
          <tr>
            <th class="text-left px-3 py-2">Référence</th>
            <th class="text-left px-3 py-2">Montant</th>
            <th class="text-left px-3 py-2">Statut</th>
            <th class="text-left px-3 py-2">Date demande</th>
            <th class="text-left px-3 py-2">Date traitement</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in payouts" :key="p.id" class="border-t border-black/10 dark:border-white/10">
            <td class="px-3 py-2 font-mono text-xs">{{ p.reference }}</td>
            <td class="px-3 py-2">{{ p.currency }} {{ p.amount.toLocaleString() }}</td>
            <td class="px-3 py-2"><StatusBadge :status="p.status" /></td>
            <td class="px-3 py-2 text-xs text-[color:var(--color-text-muted)]">{{ new Date(p.requested_at).toLocaleDateString() }}</td>
            <td class="px-3 py-2 text-xs text-[color:var(--color-text-muted)]">{{ p.completed_at ? new Date(p.completed_at).toLocaleDateString() : '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <div v-if="showRequestModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="showRequestModal = false"></div>
      <div class="relative bg-[color:var(--bg)] rounded-lg shadow-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-4">Demander un retrait</h3>
        <p class="text-sm text-[color:var(--color-text-muted)] mb-4">
          Solde disponible : {{ balance?.currency }} {{ balance?.available_balance.toLocaleString() }}
        </p>
        
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Montant (min. 1000 XOF)</label>
          <input v-model.number="requestAmount" type="number" min="1000" :max="balance?.available_balance"
                 class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 focus-ring" />
        </div>
        
        <div class="flex justify-end gap-2">
          <button @click="showRequestModal = false" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">
            Annuler
          </button>
          <button @click="requestPayout" class="btn-base h-9 px-4 rounded-md text-white bg-green-600">
            Confirmer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
