<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50" @click="close"></div>
    <div class="relative bg-[color:var(--bg)] rounded-lg shadow-lg w-full max-w-md p-6">
      <h3 class="text-xl font-semibold mb-4">{{ t('profile.change_password') }}</h3>
      <form @submit.prevent="submit">
        <div class="space-y-3">
          <div>
            <label class="block text-sm mb-1">{{ t('profile.current_password') }}</label>
            <input v-model="form.current_password" type="password" required class="input" />
          </div>
          <div>
            <label class="block text-sm mb-1">{{ t('profile.new_password') }}</label>
            <input v-model="form.password" type="password" required minlength="8" class="input" />
          </div>
          <div>
            <label class="block text-sm mb-1">{{ t('profile.confirm_password') }}</label>
            <input v-model="form.password_confirmation" type="password" required minlength="8" class="input" />
          </div>
          <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
        </div>
        <div class="mt-5 flex justify-end gap-2">
          <button type="button" @click="close" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('common.cancel') }}</button>
          <button type="submit" class="btn-base h-9 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }" :disabled="loading">
            <span v-if="!loading">{{ t('common.save') }}</span>
            <span v-else>{{ t('common.saving') }}...</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { updatePassword } from '@/lib/services/profile'

const props = defineProps<{ isOpen: boolean }>()
const emit = defineEmits<{ (e: 'close'): void; (e: 'changed'): void }>()
const { t } = useI18n()

const form = reactive({ current_password: '', password: '', password_confirmation: '' })
const error = ref<string | null>(null)
const loading = ref(false)

function close() { emit('close') }

async function submit() {
  error.value = null
  if (form.password.length < 8) { error.value = t('errors.password_min') as string; return }
  if (form.password !== form.password_confirmation) { error.value = t('errors.password_mismatch') as string; return }
  loading.value = true
  try {
    await updatePassword({ ...form })
    emit('changed')
    emit('close')
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Error'
  } finally { loading.value = false }
}
</script>
<style scoped>
.input { width: 100%; border: 1px solid color-mix(in oklab, var(--color-text) 10%, transparent); background: transparent; border-radius: .5rem; padding: .5rem .75rem }
</style>
