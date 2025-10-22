<template>
  <div class="container-px mx-auto py-8">
    <h1 class="text-3xl font-bold mb-4">{{ t('nav.profile') }}</h1>
    <div v-if="!auth.user" class="text-sm">{{ t('nav.login') }}</div>
    <div v-else class="space-y-2">
      <div><span class="text-[color:var(--color-text-muted)]">Email:</span> {{ auth.user.email }}</div>
      <div><span class="text-[color:var(--color-text-muted)]">Nom:</span> {{ auth.user.name }}</div>
      <div><span class="text-[color:var(--color-text-muted)]">Rôles:</span> {{ auth.user.roles?.map(r=>r.name).join(', ') }}</div>
    </div>
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
const { t } = useI18n()
const auth = useAuthStore()
</script>
