<template>
  <div class="container-px mx-auto py-12 max-w-md">
    <h1 class="text-3xl font-bold mb-6">{{ t('nav.login') }}</h1>
    <form class="space-y-4">
      <input type="email" placeholder="Email" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" />
      <input type="password" placeholder="Mot de passe" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" />
      <Button variant="primary" class="w-full" full>{{ t('nav.login') }}</Button>
    </form>
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import Button from '@/components/ui/Button.vue'
const { t } = useI18n()
</script>
