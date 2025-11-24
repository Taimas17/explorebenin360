<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-md border border-black/10 dark:border-white/10 bg-[color:var(--color-bg-elevated)] p-4">
      <div class="text-base font-semibold mb-3">Suspendre l'utilisateur</div>
      <form @submit.prevent="onSubmit" class="space-y-3">
        <div>
          <label class="text-xs block mb-1">Utilisateur</label>
          <div class="text-sm">{{ userName }}</div>
        </div>
        <div>
          <label for="reason" class="text-xs block mb-1">Raison</label>
          <textarea id="reason" v-model="reason" required rows="4" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring text-sm"></textarea>
        </div>
        <div>
          <label for="duration" class="text-xs block mb-1">Durée (jours)</label>
          <input id="duration" type="number" v-model.number="duration" min="1" max="365" placeholder="Permanent" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring text-sm" />
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="$emit('close')" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">Annuler</button>
          <button type="submit" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10 bg-amber-500/20 text-amber-800 dark:text-amber-300">Suspendre</button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { suspendUser } from '@/lib/services/admin-users'

const props = defineProps<{ userId: number; userName: string }>()
const emit = defineEmits<{ (e: 'close'): void; (e: 'done'): void }>()

const reason = ref('')
const duration = ref<number | undefined>(undefined)

async function onSubmit() {
  await suspendUser(props.userId, reason.value, duration.value)
  emit('done')
  emit('close')
}
</script>
