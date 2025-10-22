<template>
  <div class="container-px mx-auto py-8 space-y-6">
    <h1 class="text-3xl font-bold mb-2">{{ t('nav.destinations') }}</h1>
    <div class="flex gap-3 flex-wrap">
      <Badge>Plage</Badge>
      <Badge>Culture</Badge>
      <Badge>Nature</Badge>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <Card v-for="i in 9" :key="i">
        <template #title>Destination {{ i }}</template>
        Description placeholder
        <template #actions>
          <Button variant="primary" size="sm">Voir</Button>
        </template>
      </Card>
    </div>
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import Badge from '@/components/ui/Badge.vue'
import Card from '@/components/ui/Card.vue'
import Button from '@/components/ui/Button.vue'
const { t } = useI18n()
</script>
