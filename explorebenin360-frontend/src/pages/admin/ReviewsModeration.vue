<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Avis" title="Modération des avis" class="mb-6" />

    <div class="flex flex-wrap items-center gap-2 mb-3">
      <label class="text-xs text-[color:var(--color-text-muted)]">Statut</label>
      <select v-model="status" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
        <option value="">Tous</option>
        <option value="pending">pending</option>
        <option value="published">published</option>
        <option value="rejected">rejected</option>
      </select>
      <label class="text-xs text-[color:var(--color-text-muted)]">Type</label>
      <select v-model="type" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
        <option value="">Tous</option>
        <option value="App\\Models\\Accommodation">Accommodation</option>
        <option value="App\\Models\\Guide">Guide</option>
        <option value="App\\Models\\Offering">Offering</option>
        <option value="App\\Models\\Event">Event</option>
      </select>
      <label class="text-xs text-[color:var(--color-text-muted)]">Achat vérifié</label>
      <select v-model="verified" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
        <option :value="undefined">Tous</option>
        <option :value="true">Oui</option>
        <option :value="false">Non</option>
      </select>
    </div>

    <div class="rounded-md border border-black/10 dark:border-white/10 overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-black/5 dark:bg-white/5">
          <tr>
            <th class="text-left px-3 py-2">#</th>
            <th class="text-left px-3 py-2">Auteur</th>
            <th class="text-left px-3 py-2">Note</th>
            <th class="text-left px-3 py-2">Type</th>
            <th class="text-left px-3 py-2">Contenu</th>
            <th class="text-left px-3 py-2">Statut</th>
            <th class="text-left px-3 py-2"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in items" :key="r.id" class="border-t border-black/10 dark:border-white/10">
            <td class="px-3 py-2">{{ r.id }}</td>
            <td class="px-3 py-2">{{ r.user?.name }}</td>
            <td class="px-3 py-2">{{ r.rating }} ★</td>
            <td class="px-3 py-2">{{ r.reviewable_type.split('\\\\').pop() }}</td>
            <td class="px-3 py-2 max-w-[400px] truncate" :title="r.body">{{ r.body }}</td>
            <td class="px-3 py-2">{{ r.status }}</td>
            <td class="px-3 py-2 text-right">
              <div class="inline-flex gap-2">
                <button v-if="r.status==='pending'" @click="approve(r.id)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10">Approuver</button>
                <button v-if="r.status==='pending'" @click="rejectPrompt(r.id)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10">Rejeter</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import { fetchAdminReviews, approveReview, rejectReview } from '@/lib/services/reviews'

const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const items = ref([])
const status = ref('pending')
const type = ref('')
const verified = ref()

onMounted(load)

async function load() {
  const res = await fetchAdminReviews({ status: status.value || undefined, reviewable_type: type.value || undefined, verified_only: verified.value })
  items.value = res.data?.data || res.data || []
}
async function approve(id) { await approveReview(id); await load() }
async function rejectPrompt(id) {
  const reason = prompt('Raison du rejet ?')
  if (!reason) return
  await rejectReview(id, reason)
  await load()
}
</script>
