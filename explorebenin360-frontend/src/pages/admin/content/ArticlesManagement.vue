<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Articles" title="Gérer les articles" class="mb-6" />

    <div class="flex flex-wrap items-end gap-2 mb-4">
      <div>
        <label class="text-xs text-[color:var(--color-text-muted)]">Recherche</label>
        <input v-model="filters.q" @keyup.enter="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring" placeholder="Titre, contenu" />
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
        <label class="text-xs text-[color:var(--color-text-muted)]">Catégorie</label>
        <input v-model="filters.category" @keyup.enter="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring" placeholder="culture, nature..." />
      </div>
      <div class="ml-auto">
        <RouterLink :to="{ name: 'admin-article-create' }" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">Créer article</RouterLink>
      </div>
    </div>

    <ContentTable :items="items" :columns="columns" @edit="goEdit" @delete="onDelete" @view="goView">
      <template #cell-published_at="{ row }">
        {{ row.published_at ? new Date(row.published_at).toLocaleDateString() : '-' }}
      </template>
    </ContentTable>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import ContentTable from '@/components/admin/ContentTable.vue'
import { fetchAdminArticles, deleteArticle } from '@/lib/services/admin-content'

const router = useRouter()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const items = ref<any[]>([])
const columns = [
  { key: 'id', label: '#' },
  { key: 'title', label: 'Titre' },
  { key: 'category', label: 'Catégorie' },
  { key: 'status', label: 'Statut' },
  { key: 'published_at', label: 'Publication' },
]
const filters = ref<any>({ q: '', status: '', category: '' })

onMounted(load)
async function load() {
  const res = await fetchAdminArticles({ ...filters.value, per_page: 50 })
  items.value = res.data || res
}
function goEdit(row: any) { router.push({ name: 'admin-article-edit', params: { id: row.id } }) }
function goView(row: any) { window.open(`/blog/${row.slug}`, '_blank') }
async function onDelete(row: any) { if (confirm('Supprimer cet article ?')) { await deleteArticle(row.id); await load() } }
</script>
