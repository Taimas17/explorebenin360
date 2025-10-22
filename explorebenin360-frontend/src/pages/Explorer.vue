<template>
  <div class="container-px mx-auto py-8">
    <h1 class="text-3xl font-bold mb-4">{{ t('nav.explorer') }}</h1>
    <MapShell />
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import MapShell from '@/components/maps/MapShell.vue'
const { t } = useI18n()
</script>
