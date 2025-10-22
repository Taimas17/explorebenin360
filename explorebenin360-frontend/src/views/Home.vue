<script setup lang="ts">
import EBGallery from '@/components/media/EBGallery.vue'
import EB360Viewer from '@/components/media/EB360Viewer.vue'

const svg = (label: string, from = '#FF6B35', to = '#FFD166') =>
  `data:image/svg+xml;utf8,` + encodeURIComponent(`
  <svg xmlns='http://www.w3.org/2000/svg' width='1600' height='900' viewBox='0 0 1600 900'>
    <defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0%' stop-color='${from}'/><stop offset='100%' stop-color='${to}'/></linearGradient></defs>
    <rect width='1600' height='900' fill='url(#g)'/>
    <text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='72' font-family='Poppins, Inter, sans-serif' fill='#0b0f16'>${label}</text>
  </svg>`)

const heroItems = [
  { src: svg('Ganvié'), alt: 'Ganvié stilt village', caption: 'Ganvié stilt village at sunset' },
  { src: svg('Cotonou', '#00796B', '#FFD166'), alt: 'Cotonou beach', caption: 'Cotonou beach' },
  { src: svg('Pendjari', '#FFD166', '#FF6B35'), alt: 'Pendjari National Park', caption: 'Pendjari savannah' },
]
</script>

<template>
  <section class="mb-10">
    <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-4" style="color:#00796B">Explore Benin en 360°</h1>
    <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">Découvrez les destinations, hébergements, et guides locaux avec une expérience immersive.</p>
    <EBGallery :items="heroItems" />
  </section>

  <section class="grid md:grid-cols-2 gap-8 items-center">
    <div>
      <h2 class="text-2xl font-semibold mb-2" style="color:#FF6B35">Visitez en immersion</h2>
      <p class="text-gray-600 dark:text-gray-300">Un aperçu 360° d'une plage de Cotonou.</p>
    </div>
    <EB360Viewer :src="svg('Panorama')" alt="Plage de Cotonou" />
  </section>
</template>
