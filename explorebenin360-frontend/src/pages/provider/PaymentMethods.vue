<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchPaymentMethods, createPaymentMethod, setDefaultPaymentMethod, deletePaymentMethod } from '@/lib/services/payouts'
import type { PaymentMethod } from '@/types/business'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import Loader from '@/components/ui/Loader.vue'

const methods = ref<PaymentMethod[]>([])
const loading = ref(false)
const showAddModal = ref(false)
const form = ref({
  type: 'mobile_money' as 'bank_account' | 'mobile_money' | 'paypal',
  account_name: '',
  account_number: '',
  bank_name: '',
  mobile_provider: '',
})

async function load() {
  loading.value = true
  try {
    methods.value = await fetchPaymentMethods()
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function addMethod() {
  try {
    await createPaymentMethod(form.value as any)
    showAddModal.value = false
    form.value = {
      type: 'mobile_money',
      account_name: '',
      account_number: '',
      bank_name: '',
      mobile_provider: '',
    } as any
    load()
  } catch (e: any) {
    alert(e?.response?.data?.message || 'Erreur')
  }
}

async function setDefault(id: number) {
  try {
    await setDefaultPaymentMethod(id)
    load()
  } catch (e: any) {
    alert(e?.response?.data?.message || 'Erreur')
  }
}

async function removeMethod(id: number) {
  if (!confirm('Supprimer cette méthode de paiement ?')) return
  try {
    await deletePaymentMethod(id)
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
  <BrandBanner title="Méthodes de paiement" />
  
  <div class="container-px py-8">
    <div class="flex justify-between items-center mb-4">
      <p class="text-sm text-[color:var(--color-text-muted)]">
        Ajoutez vos coordonnées bancaires ou mobile money pour recevoir vos paiements
      </p>
      <button @click="showAddModal = true" class="btn-base h-9 px-4 rounded-md text-white"
              :style="{ backgroundColor: 'var(--color-primary)' }">
        + Ajouter
      </button>
    </div>
    
    <Loader v-if="loading" />
    
    <div v-else class="space-y-3">
      <div v-for="m in methods" :key="m.id" class="border border-black/10 dark:border-white/10 rounded-lg p-4">
        <div class="flex justify-between items-start">
          <div>
            <div class="flex items-center gap-2">
              <span class="font-medium">{{ m.account_name }}</span>
              <span v-if="m.is_default" class="text-xs px-2 py-0.5 rounded-full bg-green-500/20 text-green-600">Par défaut</span>
              <span v-if="m.is_verified" class="text-xs px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-600">Vérifié</span>
            </div>
            <div class="text-sm text-[color:var(--color-text-muted)] mt-1">
              {{ m.type === 'bank_account' ? 'Compte bancaire' : m.type === 'mobile_money' ? 'Mobile Money' : 'PayPal' }}
              • {{ m.account_number_masked }}
            </div>
            <div v-if="m.bank_name" class="text-xs text-[color:var(--color-text-muted)]">{{ m.bank_name }}</div>
            <div v-if="m.mobile_provider" class="text-xs text-[color:var(--color-text-muted)]">{{ m.mobile_provider }}</div>
          </div>
          
          <div class="flex gap-2">
            <button v-if="!m.is_default" @click="setDefault(m.id)"
                    class="btn-base h-8 px-3 rounded text-xs border border-black/10 dark:border-white/10">
              Définir par défaut
            </button>
            <button @click="removeMethod(m.id)" class="btn-base h-8 px-3 rounded text-xs text-red-600 hover:bg-red-600/10">
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="showAddModal = false"></div>
      <div class="relative bg-[color:var(--bg)] rounded-lg shadow-lg w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-semibold mb-4">Ajouter une méthode de paiement</h3>
        
        <form @submit.prevent="addMethod" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Type</label>
            <select v-model="form.type" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 focus-ring">
              <option value="mobile_money">Mobile Money</option>
              <option value="bank_account">Compte bancaire</option>
              <option value="paypal">PayPal</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">Nom du titulaire *</label>
            <input v-model="form.account_name" type="text" required
                   class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 focus-ring" />
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ form.type === 'bank_account' ? 'IBAN / Numéro de compte' : form.type === 'mobile_money' ? 'Numéro de téléphone' : 'Email PayPal' }} *
            </label>
            <input v-model="form.account_number" type="text" required
                   class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 focus-ring" />
          </div>
          
          <div v-if="form.type === 'bank_account'">
            <label class="block text-sm font-medium mb-2">Nom de la banque</label>
            <input v-model="form.bank_name" type="text"
                   class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 focus-ring" />
          </div>
          
          <div v-if="form.type === 'mobile_money'">
            <label class="block text-sm font-medium mb-2">Opérateur (MTN, Moov, etc.)</label>
            <input v-model="form.mobile_provider" type="text"
                   class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 focus-ring" />
          </div>
          
          <div class="flex justify-end gap-2">
            <button type="button" @click="showAddModal = false" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">
              Annuler
            </button>
            <button type="submit" class="btn-base h-9 px-4 rounded-md text-white"
                    :style="{ backgroundColor: 'var(--color-primary)' }">
              Ajouter
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
