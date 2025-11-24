<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Destinations" title="Gérer les destinations" class="mb-6" />

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
        <label class="text-xs text-[color:var(--color-text-muted)]">Type</label>
        <select v-model="filters.type" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
          <option value="">Tous</option>
          <option value="city">city</option>
          <option value="site">site</option>
          <option value="museum">museum</option>
          <option value="park">park</option>
          <option value="beach">beach</option>
          <option value="culture">culture</option>
          <option value="history">history</option>
          <option value="gastronomy">gastronomy</option>
          <option value="adventure">adventure</option>
          <option value="other">other</option>
        </select>
      </div>
      <div class="ml-auto">
        <RouterLink :to="{ name: 'admin-place-create' }" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">Créer destination</RouterLink>
      </div>
    </div>

    <ContentTable :items="items" :columns="columns" @edit="goEdit" @delete="onDelete" @view="goView">
      <template #cell-price_from="{ row }">{{ row.price_from ?? '-' }} {{ row.price_from ? row.currency : '' }}</template>
    </ContentTable>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import ContentTable from '@/components/admin/ContentTable.vue'
import { fetchAdminPlaces, deletePlace } from '@/lib/services/admin-content'

const router = useRouter()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const items = ref<any[]>([])
const columns = [
  { key: 'id', label: '#' },
  { key: 'title', label: 'Titre' },
  { key: 'city', label: 'Ville' },
  { key: 'type', label: 'Type' },
  { key: 'price_from', label: 'Prix dès' },
  { key: 'status', label: 'Statut' },
]
const filters = ref<any>({ q: '', status: '', type: '' })

onMounted(load)
async function load() {
  const res = await fetchAdminPlaces({ ...filters.value, per_page: 50 })
  items.value = res.data || res
}
function goEdit(row: any) { router.push({ name: 'admin-place-edit', params: { id: row.id } }) }
function goView(row: any) { window.open(`/destinations/${row.slug}`, '_blank') }
async function onDelete(row: any) { if (confirm('Supprimer cette destination ?')) { await deletePlace(row.id); await load() } }
</script>
