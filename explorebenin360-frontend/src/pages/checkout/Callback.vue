<template>
  <div class="container-px mx-auto py-12 max-w-lg text-center">
    <h1 class="text-3xl font-bold mb-2">{{ t('checkout.callback_title') }}</h1>
    <p class="text-[color:var(--color-text-muted)] mb-6">{{ t('checkout.callback_subtitle') }}</p>
    <RouterLink to="/dashboard/reservations" class="inline-flex btn-base focus-ring h-10 px-6 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
      {{ t('nav.my_reservations') }}
    </RouterLink>
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>
