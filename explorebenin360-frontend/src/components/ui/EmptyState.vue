<template>
  <div class="text-center py-16">
    <div class="mx-auto w-28 h-28 mb-4">
      <img :src="resolvedImage" :alt="decorative ? '' : resolvedAlt" :aria-hidden="decorative ? 'true' : undefined" class="w-full h-full object-contain" />
    </div>
    <h3 class="text-lg font-semibold">{{ title }}</h3>
    <p v-if="description" class="text-sm text-[color:var(--color-text-muted)] mt-1">{{ description }}</p>
    <div class="mt-4"><slot /></div>
  </div>
</template>
<script setup lang="ts">
const props = withDefaults(defineProps<{ 
  variant?: 'default' | 'search' | 'favorites' | 'bookings'
  title: string
  description?: string
  imageSrc?: string
  alt?: string
  decorative?: boolean
}>(), { variant: 'default', decorative: false })

const imageMap: Record<string,string> = {
  default: '/src/assets/brand/images/placeholders/empty-default.png',
  search: '/src/assets/brand/images/placeholders/empty-search.png',
  favorites: '/src/assets/brand/images/placeholders/empty-favorites.png',
  bookings: '/src/assets/brand/images/placeholders/empty-bookings.png',
}

const altMap: Record<string,string> = {
  default: 'Aucun contenu à afficher',
  search: 'Aucun résultat pour cette recherche',
  favorites: 'Aucun favori pour le moment',
  bookings: 'Aucune réservation disponible',
}

const resolvedImage = computed(() => props.imageSrc || imageMap[props.variant] || imageMap.default)
const resolvedAlt = computed(() => props.alt || altMap[props.variant] || altMap.default)
</script>
