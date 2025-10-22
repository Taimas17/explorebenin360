<template>
  <div class="container-px mx-auto py-8 space-y-6">
    <h1 class="text-3xl font-bold">{{ t('nav.hebergements') }}</h1>

    <section>
      <EBGallery :items="items" />
    </section>

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
import EBGallery from '@/components/media/EBGallery.vue'

const { t } = useI18n()

const svg = (label, from = '#00796B', to = '#FFD166') =>
  'data:image/svg+xml;utf8,' + encodeURIComponent(`
  <svg xmlns='http://www.w3.org/2000/svg' width='1600' height='900' viewBox='0 0 1600 900'>
    <defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0%' stop-color='${from}'/><stop offset='100%' stop-color='${to}'/></linearGradient></defs>
    <rect width='1600' height='900' fill='url(#g)'/>
    <text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='56' font-family='Poppins, Inter, sans-serif' fill='#0b0f16'>${label}</text>
  </svg>`)

const items = [
  { src: svg('Suite moderne'), alt: "Chambre d'hôtel", caption: 'Suite moderne' },
  { src: svg('Lodge nature', '#FFD166', '#00796B'), alt: 'Lodge', caption: 'Lodge nature' },
  { src: svg('Auberge', '#FF6B35', '#FFD166'), alt: 'Auberge', caption: 'Auberge conviviale' },
]
</script>
