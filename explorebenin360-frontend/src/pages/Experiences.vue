<template>
  <div class="container-px mx-auto py-8">
    <h1 class="text-3xl font-bold mb-4">{{ t('nav.experiences') }}</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <Card v-for="i in 6" :key="i">
        <template #title>Expérience {{ i }}</template>
        Placeholder description
        <template #actions>
          <div class="flex gap-2">
            <Badge>Aventure</Badge>
            <Badge>Cult.</Badge>
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import Card from '@/components/ui/Card.vue'
import Badge from '@/components/ui/Badge.vue'
const { t } = useI18n()
</script>
