<template>
  <div class="container-px mx-auto py-12 max-w-md">
    <h1 class="text-3xl font-bold mb-6">{{ t('nav.register') }}</h1>
    <form class="space-y-4" @submit.prevent="submit">
      <input v-model="name" type="text" :placeholder="t('auth.name')" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" required />
      <input v-model="email" type="email" placeholder="Email" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" required />
      <input v-model="password" type="password" :placeholder="t('auth.password')" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" required />
      <Button variant="primary" class="w-full" :disabled="loading" full>{{ loading ? t('common.loading') : t('nav.register') }}</Button>
    </form>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import Button from '@/components/ui/Button.vue'
import { useAuthStore } from '@/stores/auth'

const { t } = useI18n()
const auth = useAuthStore()
const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const loading = ref(false)

const submit = async () => {
  loading.value = true
  try {
    await auth.register({ name: name.value, email: email.value, password: password.value })
    router.push('/profile')
  } catch (e) {
    alert(t('errors.register_failed'))
  } finally { loading.value = false }
}
</script>
