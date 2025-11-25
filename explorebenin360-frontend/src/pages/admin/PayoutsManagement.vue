<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchAdminPayouts, processPayout, completePayout, failPayout } from '@/lib/services/payouts'
import type { Payout } from '@/types/business'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import Loader from '@/components/ui/Loader.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'

const items = ref<Payout[]>([])
const loading = ref(false)
const statusFilter = ref<'' | 'pending' | 'processing' | 'completed' | 'failed' | 'cancelled'>('')

async function load() {
  loading.value = true
  try {
    const { data } = await fetchAdminPayouts({
      status: statusFilter.value || undefined,
      per_page: 50,
    })
    items.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function handleProcess(id: number) {
  const ref = prompt('Référence de transaction (optionnel) :')
  try {
    await processPayout(id, { transaction_ref: ref || undefined })
    load()
  } catch (e: any) {
    alert(e?.response?.data?.message || 'Erreur')
  }
}

async function handleComplete(id: number) {
  if (!confirm('Marquer ce payout comme complété ?')) return
  try {
    await completePayout(id)
    load()
  } catch (e: any) {
    alert(e?.response?.data?.message || 'Erreur')
  }
}

async function handleFail(id: number) {
  const reason = prompt("Raison de l'échec :")
  if (!reason) return
  try {
    await failPayout(id, { failure_reason: reason })
    load()
  } catch (e: any) {
    alert(e?.response?.data?.message || 'Erreur')
  }
}

onMounted(() => {
  load()
})
</script>

<template>
  <BrandBanner title="Gestion des Retraits" />
  
  <div class="container-px py-8">
    <div class="flex gap-2 items-center mb-4">
      <label class="text-xs text-[color:var(--color-text-muted)]">Statut</label>
      <select v-model="statusFilter" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
        <option value="">Tous</option>
        <option value="pending">En attente</option>
        <option value="processing">En cours</option>
        <option value="completed">Complété</option>
        <option value="failed">Échoué</option>
        <option value="cancelled">Annulé</option>
      </select>
    </div>
    
    <Loader v-if="loading" />
    
    <div v-else>
      <table class="min-w-full text-sm">
        <thead class="bg-black/5 dark:bg-white/5">
          <tr>
            <th class="text-left px-3 py-2">#</th>
            <th class="text-left px-3 py-2">Provider</th>
            <th class="text-left px-3 py-2">Référence</th>
            <th class="text-left px-3 py-2">Montant</th>
            <th class="text-left px-3 py-2">Statut</th>
            <th class="text-left px-3 py-2">Méthode</th>
            <th class="text-left px-3 py-2">Date</th>
            <th class="text-left px-3 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in items" :key="p.id" class="border-t border-black/10 dark:border-white/10">
            <td class="px-3 py-2">{{ p.id }}</td>
            <td class="px-3 py-2">
              <div class="font-medium">{{ p.provider_id }}</div>
              <div class="text-xs text-[color:var(--color-text-muted)]">{{ p.payment_method?.account_name }}</div>
            </td>
            <td class="px-3 py-2 font-mono text-xs">{{ p.reference }}</td>
            <td class="px-3 py-2">{{ p.currency }} {{ p.amount.toLocaleString() }}</td>
            <td class="px-3 py-2"><StatusBadge :status="p.status" /></td>
            <td class="px-3 py-2 text-xs">
              {{ p.payment_method?.type }}
              <div class="text-[color:var(--color-text-muted)]">{{ p.payment_method?.account_number_masked }}</div>
            </td>
            <td class="px-3 py-2 text-xs text-[color:var(--color-text-muted)]">{{ new Date(p.requested_at).toLocaleDateString() }}</td>
            <td class="px-3 py-2">
              <div class="flex gap-1">
                <button v-if="p.status === 'pending'" @click="handleProcess(p.id)"
                        class="btn-base h-8 px-2 rounded text-xs text-blue-600 hover:bg-blue-600/10">
                  Traiter
                </button>
                <button v-if="p.status === 'pending' || p.status === 'processing'" @click="handleComplete(p.id)"
                        class="btn-base h-8 px-2 rounded text-xs text-green-600 hover:bg-green-600/10">
                  Compléter
                </button>
                <button v-if="p.status === 'pending' || p.status === 'processing'" @click="handleFail(p.id)"
                        class="btn-base h-8 px-2 rounded text-xs text-red-600 hover:bg-red-600/10">
                  Échec
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
