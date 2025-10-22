<script setup lang="ts">
import EBImage from '@/components/media/EBImage.vue'

const svg = (label: string, from = '#FF6B35', to = '#FFD166') =>
  `data:image/svg+xml;utf8,` + encodeURIComponent(`
  <svg xmlns='http://www.w3.org/2000/svg' width='1600' height='900' viewBox='0 0 1600 900'>
    <defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0%' stop-color='${from}'/><stop offset='100%' stop-color='${to}'/></linearGradient></defs>
    <rect width='1600' height='900' fill='url(#g)'/>
    <text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='64' font-family='Poppins, Inter, sans-serif' fill='#0b0f16'>${label}</text>
  </svg>`)
</script>

<template>
  <h1 class="text-3xl font-bold mb-6">Destinations</h1>
  <div class="grid md:grid-cols-3 gap-6">
    <EBImage :src="svg('Ganvié')" alt="Ganvié" :width="1200" :height="800" class="h-48" />
    <EBImage :src="svg('Cotonou', '#00796B', '#FFD166')" alt="Cotonou" :width="1200" :height="800" class="h-48" />
    <EBImage :src="svg('Pendjari', '#FFD166', '#FF6B35')" alt="Pendjari" :width="1200" :height="800" class="h-48" />
  </div>
</template>
