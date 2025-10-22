<template>
  <div class="container-px mx-auto py-8 space-y-6">
    <h1 class="text-3xl font-bold mb-2">{{ t('nav.destinations') }}</h1>

    <section>
      <div class="grid md:grid-cols-3 gap-4">
        <EBImage :src="svg('Ganvié')" alt="Ganvié" :width="1200" :height="800" class="h-40" />
        <EBImage :src="svg('Cotonou', '#00796B', '#FFD166')" alt="Cotonou" :width="1200" :height="800" class="h-40" />
        <EBImage :src="svg('Pendjari', '#FFD166', '#FF6B35')" alt="Pendjari" :width="1200" :height="800" class="h-40" />
      </div>
    </section>

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
import EBImage from '@/components/media/EBImage.vue'

const { t } = useI18n()

const svg = (label, from = '#FF6B35', to = '#FFD166') =>
  'data:image/svg+xml;utf8,' + encodeURIComponent(`
  <svg xmlns='http://www.w3.org/2000/svg' width='1600' height='900' viewBox='0 0 1600 900'>
    <defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0%' stop-color='${from}'/><stop offset='100%' stop-color='${to}'/></linearGradient></defs>
    <rect width='1600' height='900' fill='url(#g)'/>
    <text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='64' font-family='Poppins, Inter, sans-serif' fill='#0b0f16'>${label}</text>
  </svg>`)
</script>
