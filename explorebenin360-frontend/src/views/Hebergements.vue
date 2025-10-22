<script setup lang="ts">
import EBGallery from '@/components/media/EBGallery.vue'

const svg = (label: string, from = '#00796B', to = '#FFD166') =>
  `data:image/svg+xml;utf8,` + encodeURIComponent(`
  <svg xmlns='http://www.w3.org/2000/svg' width='1600' height='900' viewBox='0 0 1600 900'>
    <defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0%' stop-color='${from}'/><stop offset='100%' stop-color='${to}'/></linearGradient></defs>
    <rect width='1600' height='900' fill='url(#g)'/>
    <text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='64' font-family='Poppins, Inter, sans-serif' fill='#0b0f16'>${label}</text>
  </svg>`)

const items = [
  { src: svg('Suite moderne'), alt: "Chambre d'hôtel", caption: 'Suite moderne' },
  { src: svg('Lodge nature', '#FFD166', '#00796B'), alt: 'Lodge', caption: 'Lodge nature' },
  { src: svg('Auberge', '#FF6B35', '#FFD166'), alt: 'Auberge', caption: 'Auberge conviviale' },
]
</script>

<template>
  <h1 class="text-3xl font-bold mb-6">Hébergements</h1>
  <EBGallery :items="items" />
</template>
