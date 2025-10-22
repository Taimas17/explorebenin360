<template>
  <div class="container-px mx-auto py-8 space-y-6">
    <h1 class="text-3xl font-bold">{{ t('nav.blog') }}</h1>

    <article class="prose dark:prose-invert max-w-none">
      <h2>Voyager à Ganvié</h2>
      <p>Découvrez des conseils pratiques pour visiter la Venise d'Afrique.</p>
      <EBImage :src="svg('Bateaux à Ganvié')" alt="Bateaux à Ganvié" :width="1200" :height="800" />
    </article>

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
import EBImage from '@/components/media/EBImage.vue'

const { t } = useI18n()

const svg = (label) => 'data:image/svg+xml;utf8,' + encodeURIComponent(`
  <svg xmlns='http://www.w3.org/2000/svg' width='1600' height='900' viewBox='0 0 1600 900'>
    <rect width='1600' height='900' fill='#FFD166'/>
    <text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='64' font-family='Poppins, Inter, sans-serif' fill='#0b0f16'>${label}</text>
  </svg>`)
</script>
