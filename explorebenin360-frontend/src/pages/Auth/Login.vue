<template>
  <div class="container-px mx-auto py-12 max-w-md">
    <h1 class="text-3xl font-bold mb-6">{{ t('nav.login') }}</h1>
    <form class="space-y-4" @submit.prevent="submit">
      <input v-model="email" type="email" placeholder="Email" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" required />
      <input v-model="password" type="password" :placeholder="t('auth.password')" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" required />
      <Button variant="primary" class="w-full" :disabled="loading" full>{{ loading ? t('common.loading') : t('nav.login') }}</Button>
    </form>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import Button from '@/components/ui/Button.vue'
import { useAuthStore } from '@/stores/auth'
import { safeRedirect } from '@/utils/urlValidation'

const { t } = useI18n()
const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const email = ref('')
const password = ref('')
const loading = ref(false)

const submit = async () => {
  loading.value = true
  try {
    await auth.login({ email: email.value, password: password.value })
    const q = route.query.redirect
    const redirectTo = safeRedirect(typeof q === 'string' ? q : null, '/')
    router.push(redirectTo)
  } catch (e) {
    alert(t('errors.login_failed'))
  } finally { loading.value = false }
}
</script>
