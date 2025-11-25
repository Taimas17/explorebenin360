<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchAdminReviews, approveReview, rejectReview, deleteReviewAdmin } from '@/lib/services/reviews'
import type { Review } from '@/types/business'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import Loader from '@/components/ui/Loader.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import StarRating from '@/components/reviews/StarRating.vue'

const items = ref<Review[]>([])
const loading = ref(false)
const statusFilter = ref<'' | 'pending' | 'approved' | 'rejected'>('')

async function load() {
  loading.value = true
  try {
    const { data } = await fetchAdminReviews({
      status: statusFilter.value || undefined,
      per_page: 20,
    })
    items.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function handleApprove(id: number) {
  if (!confirm('Approuver cet avis ?')) return
  try {
    await approveReview(id)
    load()
  } catch (e: any) {
    alert(e?.response?.data?.message || 'Erreur')
  }
}

async function handleReject(id: number) {
  if (!confirm('Rejeter cet avis ?')) return
  try {
    await rejectReview(id)
    load()
  } catch (e: any) {
    alert(e?.response?.data?.message || 'Erreur')
  }
}

async function handleDelete(id: number) {
  if (!confirm('Supprimer définitivement cet avis ?')) return
  try {
    await deleteReviewAdmin(id)
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
  <BrandBanner title="Gestion des Avis" />
  <div class="container-px py-8">
    <div class="flex gap-2 items-center mb-4">
      <label class="text-xs text-[color:var(--color-text-muted)]">Statut</label>
      <select v-model="statusFilter" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
        <option value="">Tous</option>
        <option value="pending">En attente</option>
        <option value="approved">Approuvés</option>
        <option value="rejected">Rejetés</option>
      </select>
    </div>
    <Loader v-if="loading" />
    <div v-else>
      <table class="min-w-full text-sm">
        <thead class="bg-black/5 dark:bg-white/5">
          <tr>
            <th class="text-left px-3 py-2">#</th>
            <th class="text-left px-3 py-2">Utilisateur</th>
            <th class="text-left px-3 py-2">Note</th>
            <th class="text-left px-3 py-2">Commentaire</th>
            <th class="text-left px-3 py-2">Statut</th>
            <th class="text-left px-3 py-2">Date</th>
            <th class="text-left px-3 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in items" :key="r.id" class="border-t border-black/10 dark:border-white/10">
            <td class="px-3 py-2">{{ r.id }}</td>
            <td class="px-3 py-2">{{ r.user.name }}</td>
            <td class="px-3 py-2"><StarRating :rating="r.rating" readonly size="sm" /></td>
            <td class="px-3 py-2 max-w-xs truncate">{{ r.comment || '-' }}</td>
            <td class="px-3 py-2"><StatusBadge :status="r.status" /></td>
            <td class="px-3 py-2 text-xs text-[color:var(--color-text-muted)]">{{ new Date(r.created_at).toLocaleDateString() }}</td>
            <td class="px-3 py-2">
              <div class="flex gap-1">
                <button v-if="r.status === 'pending'" @click="handleApprove(r.id)" class="btn-base h-8 px-2 rounded text-xs text-green-600 hover:bg-green-600/10">Approuver</button>
                <button v-if="r.status === 'pending'" @click="handleReject(r.id)" class="btn-base h-8 px-2 rounded text-xs text-red-600 hover:bg-red-600/10">Rejeter</button>
                <button @click="handleDelete(r.id)" class="btn-base h-8 px-2 rounded text-xs text-red-600 hover:bg-red-600/10">Supprimer</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
