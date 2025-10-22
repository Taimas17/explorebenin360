<template>
  <div class="container-px mx-auto py-8 max-w-3xl">
    <h1 class="text-3xl font-bold mb-6">{{ t('nav.contact') }}</h1>
    <form class="space-y-4">
      <input type="text" placeholder="Nom" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" />
      <input type="email" placeholder="Email" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" />
      <textarea rows="5" placeholder="Message" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2"></textarea>
      <Button variant="primary">Envoyer</Button>
    </form>
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import Button from '@/components/ui/Button.vue'
const { t } = useI18n()
</script>
