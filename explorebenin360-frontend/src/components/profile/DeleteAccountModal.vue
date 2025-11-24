<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50" @click="close"></div>
    <div class="relative bg-[color:var(--bg)] rounded-lg shadow-lg w-full max-w-md p-6">
      <h3 class="text-xl font-semibold mb-2">{{ t('profile.delete_account') }}</h3>
      <p class="text-sm text-[color:var(--color-text-muted)] mb-4">{{ t('profile.delete_warning') }}</p>
      <form @submit.prevent="submit">
        <div class="space-y-3">
          <div>
            <label class="block text-sm mb-1">{{ t('profile.password_confirm') }}</label>
            <input v-model="password" type="password" required class="input" />
          </div>
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" v-model="confirm" />
            <span>{{ t('profile.delete_ack') }}</span>
          </label>
          <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
        </div>
        <div class="mt-5 flex justify-end gap-2">
          <button type="button" @click="close" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('common.cancel') }}</button>
          <button type="submit" class="btn-base h-9 px-4 rounded-md text-white bg-red-600 focus-ring" :disabled="loading || !confirm">
            <span v-if="!loading">{{ t('profile.delete_account') }}</span>
            <span v-else>{{ t('common.processing') }}...</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { deleteAccount } from '@/lib/services/profile'

const props = defineProps<{ isOpen: boolean }>()
const emit = defineEmits<{ (e: 'close'): void; (e: 'deleted'): void }>()
const { t } = useI18n()

const password = ref('')
const confirm = ref(false)
const loading = ref(false)
const error = ref<string | null>(null)

function close() { emit('close') }

async function submit() {
  error.value = null
  if (!confirm.value) return
  loading.value = true
  try {
    await deleteAccount(password.value)
    emit('deleted')
    emit('close')
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Error'
  } finally { loading.value = false }
}
</script>
<style scoped>
.input { width: 100%; border: 1px solid color-mix(in oklab, var(--color-text) 10%, transparent); background: transparent; border-radius: .5rem; padding: .5rem .75rem }
</style>
