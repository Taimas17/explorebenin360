<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Hébergements" title="Gérer les hébergements" class="mb-6" />

    <div class="flex flex-wrap items-end gap-2 mb-4">
      <div>
        <label class="text-xs text-[color:var(--color-text-muted)]">Recherche</label>
        <input v-model="filters.q" @keyup.enter="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring" placeholder="Titre, description" />
      </div>
      <div>
        <label class="text-xs text-[color:var(--color-text-muted)]">Statut</label>
        <select v-model="filters.status" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
          <option value="">Tous</option>
          <option value="published">published</option>
          <option value="draft">draft</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-[color:var(--color-text-muted)]">Ville</label>
        <input v-model="filters.city" @keyup.enter="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring" placeholder="Cotonou" />
      </div>
      <div class="ml-auto">
        <RouterLink :to="{ name: 'admin-accommodation-create' }" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">Créer hébergement</RouterLink>
      </div>
    </div>

    <ContentTable :items="items" :columns="columns" @edit="goEdit" @delete="onDelete" @view="goView">
      <template #cell-price_per_night="{ row }">
        {{ row.price_per_night }} {{ row.currency }}
      </template>
    </ContentTable>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import ContentTable from '@/components/admin/ContentTable.vue'
import { fetchAdminAccommodations, deleteAccommodation } from '@/lib/services/admin-content'

const router = useRouter()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const items = ref<any[]>([])
const columns = [
  { key: 'id', label: '#' },
  { key: 'title', label: 'Titre' },
  { key: 'city', label: 'Ville' },
  { key: 'type', label: 'Type' },
  { key: 'price_per_night', label: 'Prix' },
  { key: 'status', label: 'Statut' },
]
const filters = ref<any>({ q: '', status: '', city: '' })

onMounted(load)
async function load() {
  const res = await fetchAdminAccommodations({ ...filters.value, per_page: 50 })
  items.value = res.data || res
}
function goEdit(row: any) { router.push({ name: 'admin-accommodation-edit', params: { id: row.id } }) }
function goView(row: any) { window.open(`/hebergements/${row.slug}`, '_blank') }
async function onDelete(row: any) { if (confirm('Supprimer cet hébergement ?')) { await deleteAccommodation(row.id); await load() } }
</script>
