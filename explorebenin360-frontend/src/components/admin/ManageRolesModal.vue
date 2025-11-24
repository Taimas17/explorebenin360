<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-md border border-black/10 dark:border-white/10 bg-[color:var(--color-bg-elevated)] p-4">
      <div class="text-base font-semibold mb-3">Gérer les rôles</div>
      <form @submit.prevent="onSubmit" class="space-y-3">
        <div class="flex items-center gap-2">
          <input id="role-traveler" type="checkbox" v-model="rolesMap.traveler" />
          <label for="role-traveler">traveler</label>
        </div>
        <div class="flex items-center gap-2">
          <input id="role-provider" type="checkbox" v-model="rolesMap.provider" />
          <label for="role-provider">provider</label>
        </div>
        <div class="flex items-center gap-2">
          <input id="role-admin" type="checkbox" v-model="rolesMap.admin" />
          <label for="role-admin">admin</label>
        </div>
        <p class="text-xs text-amber-600 dark:text-amber-400">
          Retirer le rôle provider va désactiver toutes les offres.
        </p>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="$emit('close')" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">Annuler</button>
          <button type="submit" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { reactive } from 'vue'
import { updateUserRoles } from '@/lib/services/admin-users'

const props = defineProps<{ userId: number; currentRoles: string[] }>()
const emit = defineEmits<{ (e: 'close'): void; (e: 'updated', roles: string[]): void }>()

const rolesMap = reactive({
  traveler: props.currentRoles.includes('traveler'),
  provider: props.currentRoles.includes('provider'),
  admin: props.currentRoles.includes('admin'),
})

async function onSubmit() {
  const roles = Object.entries(rolesMap).filter(([,v]) => v).map(([k]) => k)
  const newRoles = await updateUserRoles(props.userId, roles)
  emit('updated', newRoles)
  emit('close')
}
</script>
