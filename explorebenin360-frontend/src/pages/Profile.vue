<template>
  <div class="container-px mx-auto py-8">
    <h1 class="text-3xl font-bold mb-2">{{ t('nav.profile') }}</h1>
    <Alert type="info">Profil utilisateur à venir</Alert>
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import Alert from '@/components/ui/Alert.vue'
const { t } = useI18n()
</script>
