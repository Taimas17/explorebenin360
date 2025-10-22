<template>
  <div class="container-px mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">{{ t('nav.guides') }}</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <Card v-for="i in 6" :key="i">
        <template #title>Guide {{ i }}</template>
        Bref descriptif du guide
        <template #actions>
          <Button size="sm" variant="outline">Contacter</Button>
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
