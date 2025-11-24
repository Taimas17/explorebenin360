<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Utilisateurs" title="Gestion des utilisateurs" class="mb-6" />

    <div class="flex flex-wrap gap-2 items-end mb-3">
      <div>
        <label class="text-xs block mb-1">Recherche</label>
        <input v-model="filters.q" @keyup.enter="load" placeholder="Nom ou email" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring" />
      </div>
      <div>
        <label class="text-xs block mb-1">Rôle</label>
        <select v-model="filters.role" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
          <option value="">Tous</option>
          <option value="traveler">traveler</option>
          <option value="provider">provider</option>
          <option value="admin">admin</option>
        </select>
      </div>
      <div>
        <label class="text-xs block mb-1">Statut compte</label>
        <select v-model="filters.account_status" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
          <option value="">Tous</option>
          <option value="active">active</option>
          <option value="suspended">suspended</option>
          <option value="banned">banned</option>
        </select>
      </div>
      <div>
        <label class="text-xs block mb-1">Statut provider</label>
        <select v-model="filters.provider_status" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
          <option value="">Tous</option>
          <option value="none">none</option>
          <option value="pending">pending</option>
          <option value="approved">approved</option>
          <option value="rejected">rejected</option>
        </select>
      </div>
      <div>
        <label class="text-xs block mb-1">Tri</label>
        <select v-model="filters.sort" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
          <option value="recent">Plus récents</option>
          <option value="name">Nom</option>
          <option value="email">Email</option>
          <option value="login_count">Connexions</option>
        </select>
      </div>
      <div class="ml-auto">
        <button @click="load" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">Filtrer</button>
      </div>
    </div>

    <div class="rounded-md border border-black/10 dark:border-white/10 overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-black/5 dark:bg-white/5">
          <tr>
            <th class="text-left px-3 py-2">#</th>
            <th class="text-left px-3 py-2">Nom</th>
            <th class="text-left px-3 py-2">Email</th>
            <th class="text-left px-3 py-2">Rôles</th>
            <th class="text-left px-3 py-2">Statut</th>
            <th class="text-left px-3 py-2">Connexions</th>
            <th class="text-left px-3 py-2">Dernière connexion</th>
            <th class="text-left px-3 py-2"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in items" :key="u.id" class="border-t border-black/10 dark:border-white/10">
            <td class="px-3 py-2">{{ u.id }}</td>
            <td class="px-3 py-2">
              <RouterLink :to="{ name: 'admin-user-detail', params: { id: u.id } }" class="link">{{ u.name }}</RouterLink>
            </td>
            <td class="px-3 py-2">{{ u.email }}</td>
            <td class="px-3 py-2">
              <div class="flex gap-1 flex-wrap">
                <span v-for="r in u.roles" :key="r" class="inline-flex items-center rounded-md bg-black/5 dark:bg-white/5 px-2 py-0.5 text-xs">{{ r }}</span>
              </div>
            </td>
            <td class="px-3 py-2"><StatusBadge :status="u.account_status" /></td>
            <td class="px-3 py-2">{{ u.login_count }}</td>
            <td class="px-3 py-2">{{ u.last_login_at || '—' }}</td>
            <td class="px-3 py-2 text-right">
              <div class="inline-flex gap-2">
                <RouterLink :to="{ name: 'admin-user-detail', params: { id: u.id } }" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10">Voir</RouterLink>
                <button @click="openSuspend(u)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10">Suspendre</button>
                <button @click="ban(u)" class="btn-base focus-ring h-8 px-3 rounded-md border border-red-500/30 text-red-600">Bannir</button>
                <button @click="remove(u)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10">Supprimer</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex items-center justify-between mt-3 text-sm">
      <div>Total: {{ meta.total }}</div>
      <div class="flex items-center gap-2">
        <button :disabled="page<=1" @click="page--; load()" class="btn-base h-8 px-3 rounded-md border border-black/10 dark:border-white/10">Préc.</button>
        <span>Page {{ meta.current_page }}</span>
        <button :disabled="items.length < filters.per_page" @click="page++; load()" class="btn-base h-8 px-3 rounded-md border border-black/10 dark:border-white/10">Suiv.</button>
      </div>
    </div>

    <SuspendUserModal v-if="showSuspend" :user-id="selected?.id!" :user-name="selected?.name!" @done="load" @close="showSuspend=false" />
  </div>
</template>
<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import SuspendUserModal from '@/components/admin/SuspendUserModal.vue'
import { fetchAdminUsers, banUser, deleteUser } from '@/lib/services/admin-users'
import type { User } from '@/types/business'

const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const items = ref<User[]>([])
const meta = reactive({ total: 0, current_page: 1, per_page: 20 })
const page = ref(1)
const filters = reactive<{ q?: string; role?: string; account_status?: string; provider_status?: string; sort?: string; per_page: number }>({ sort: 'recent', per_page: 20 })

const showSuspend = ref(false)
const selected = ref<User | null>(null)

onMounted(load)
watch(page, load)

async function load() {
  const res = await fetchAdminUsers({ ...filters, page: page.value })
  items.value = res.data
  meta.total = res.meta.total
  meta.current_page = res.meta.current_page
  meta.per_page = res.meta.per_page
}

function openSuspend(u: User) { selected.value = u; showSuspend.value = true }
async function ban(u: User) { if (confirm(`Bannir ${u.name} ?`)) { await banUser(u.id, 'Violation des règles'); await load() } }
async function remove(u: User) { if (confirm(`Supprimer ${u.name} ?`)) { await deleteUser(u.id); await load() } }
</script>
