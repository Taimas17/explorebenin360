<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Détail Utilisateur" :title="user?.name || 'Utilisateur'" class="mb-6" />

    <div v-if="user" class="grid lg:grid-cols-3 gap-4">
      <div class="lg:col-span-2 space-y-4">
        <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
          <div class="text-sm font-medium mb-2">Informations personnelles</div>
          <div class="grid md:grid-cols-2 gap-3 text-sm">
            <div><div class="text-xs text-muted">Nom</div><div>{{ user.name }}</div></div>
            <div><div class="text-xs text-muted">Email</div><div>{{ user.email }}</div></div>
            <div><div class="text-xs text-muted">Téléphone</div><div>{{ user.phone || '—' }}</div></div>
            <div><div class="text-xs text-muted">Entreprise</div><div>{{ user.business_name || '—' }}</div></div>
            <div class="md:col-span-2">
              <div class="text-xs text-muted">Bio</div>
              <div>{{ user.bio || '—' }}</div>
            </div>
          </div>
        </div>

        <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
          <div class="text-sm font-medium mb-2">Statistiques</div>
          <div class="grid md:grid-cols-3 gap-3 text-sm">
            <div><div class="text-xs text-muted">Réservations</div><div>{{ user.bookings_count ?? 0 }}</div></div>
            <div><div class="text-xs text-muted">Favoris</div><div>{{ user.favorites_count ?? 0 }}</div></div>
            <div><div class="text-xs text-muted">Offres</div><div>{{ user.offerings_count ?? 0 }}</div></div>
          </div>
        </div>

        <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
          <div class="text-sm font-medium mb-2">Historique</div>
          <div class="grid md:grid-cols-3 gap-3 text-sm">
            <div><div class="text-xs text-muted">Créé le</div><div>{{ user.created_at }}</div></div>
            <div><div class="text-xs text-muted">Dernière connexion</div><div>{{ user.last_login_at || '—' }}</div></div>
            <div><div class="text-xs text-muted">Connexions</div><div>{{ user.login_count }}</div></div>
          </div>
        </div>
      </div>

      <div class="space-y-4">
        <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
          <div class="text-sm font-medium mb-2">Rôles</div>
          <div class="flex gap-1 flex-wrap mb-3">
            <span v-for="r in user.roles" :key="r" class="inline-flex items-center rounded-md bg-black/5 dark:bg-white/5 px-2 py-0.5 text-xs">{{ r }}</span>
          </div>
          <button class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10" @click="showRoles=true">Gérer les rôles</button>
        </div>

        <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
          <div class="text-sm font-medium mb-2">Statut du compte</div>
          <div class="flex items-center gap-2 mb-2">
            <StatusBadge :status="user.account_status" />
            <span v-if="user.account_status!=='active' && user.suspension_reason" class="text-xs text-muted">{{ user.suspension_reason }}</span>
          </div>
          <div class="flex gap-2">
            <button v-if="user.account_status==='active'" @click="showSuspend=true" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">Suspendre</button>
            <button v-if="user.account_status==='suspended'" @click="unsuspend" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">Réactiver</button>
            <button v-if="user.account_status!=='banned'" @click="ban" class="btn-base h-9 px-3 rounded-md border border-red-500/30 text-red-600">Bannir</button>
          </div>
        </div>

        <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
          <div class="text-sm font-medium mb-2">Actions</div>
          <div class="flex gap-2">
            <button @click="sendReset" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">Envoyer reset password</button>
            <button @click="remove" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">Supprimer</button>
          </div>
        </div>
      </div>
    </div>

    <ManageRolesModal v-if="showRoles && user" :user-id="user.id" :current-roles="user.roles" @updated="(r)=> user!.roles = r as any" @close="showRoles=false" />
    <SuspendUserModal v-if="showSuspend && user" :user-id="user.id" :user-name="user.name" @done="reload" @close="showSuspend=false" />
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import ManageRolesModal from '@/components/admin/ManageRolesModal.vue'
import SuspendUserModal from '@/components/admin/SuspendUserModal.vue'
import { fetchUserById, unsuspendUser, banUser, deleteUser, resetUserPassword } from '@/lib/services/admin-users'
import type { User } from '@/types/business'

const route = useRoute()
const router = useRouter()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const user = ref<User | null>(null)
const showRoles = ref(false)
const showSuspend = ref(false)

onMounted(reload)

async function reload() {
  user.value = await fetchUserById(parseInt(route.params.id as string, 10))
}

async function unsuspend() { if (!user.value) return; await unsuspendUser(user.value.id); await reload() }
async function ban() { if (!user.value) return; if (confirm('Bannir cet utilisateur ?')) { await banUser(user.value.id, 'Violation des règles'); await reload() } }
async function remove() { if (!user.value) return; if (confirm('Supprimer cet utilisateur ?')) { await deleteUser(user.value.id); router.push({ name: 'admin-users' }) } }
async function sendReset() { if (!user.value) return; await resetUserPassword(user.value.id); alert('Email de réinitialisation envoyé'); }
</script>
