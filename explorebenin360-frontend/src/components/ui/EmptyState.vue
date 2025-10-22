<template>
  <div class="text-center py-16">
    <div class="mx-auto w-28 h-28 mb-4">
      <img :src="imageSrc" alt="" class="w-full h-full object-contain" />
    </div>
    <h3 class="text-lg font-semibold">{{ title }}</h3>
    <p v-if="description" class="text-sm text-[color:var(--color-text-muted)] mt-1">{{ description }}</p>
    <div class="mt-4"><slot /></div>
  </div>
</template>
<script setup lang="ts">
const props = withDefaults(defineProps<{ variant?: 'default' | 'search' | 'favorites' | 'bookings'; title: string; description?: string }>(), {
  variant: 'default',
})

const map: Record<string,string> = {
  default: '/src/assets/brand/images/placeholders/empty-default.png',
  search: '/src/assets/brand/images/placeholders/empty-default.png',
  favorites: '/src/assets/brand/images/placeholders/empty-default.png',
  bookings: '/src/assets/brand/images/placeholders/empty-default.png',
}

const imageSrc = computed(() => map[props.variant] || map.default)
</script>
