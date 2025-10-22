<template>
  <div class="container-px mx-auto py-8">
    <h1 class="text-3xl font-bold mb-4">{{ t('nav.hebergements') }}</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <Card v-for="i in 6" :key="i">
        <template #title>Hébergement {{ i }}</template>
        Placeholder description
        <template #actions>
          <Button variant="secondary" size="sm">Réserver</Button>
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
