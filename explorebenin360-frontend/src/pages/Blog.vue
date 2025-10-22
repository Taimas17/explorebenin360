<template>
  <div class="container-px mx-auto py-8">
    <h1 class="text-3xl font-bold mb-4">{{ t('nav.blog') }}</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <Card v-for="i in 4" :key="i">
        <template #title>Article {{ i }}</template>
        Aperçu de l'article
        <template #actions>
          <Button size="sm" variant="outline">Lire</Button>
        </template>
      </Card>
    </div>
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import Card from '@/components/ui/Card.vue'
import Button from '@/components/ui/Button.vue'
const { t } = useI18n()
</script>
