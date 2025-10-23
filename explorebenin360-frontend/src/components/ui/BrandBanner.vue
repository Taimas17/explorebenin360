<template>
  <section class="relative w-full overflow-hidden rounded-[var(--radius-lg)]">
    <EBImage :src="src" :alt="alt" :width="1600" :height="900" aspect-ratio="16 / 9" class="w-full h-[36vh] md:h-[44vh] object-cover" :priority="priority" />
    <div class="absolute inset-0" :class="overlayClass"></div>
    <div class="absolute top-3 right-3 z-10"><slot name="overlay" /></div>
    <div class="absolute inset-0 flex items-end">
      <div class="container-px mx-auto py-6">
        <div class="max-w-3xl">
          <h1 v-if="title" class="text-3xl md:text-4xl font-bold text-white drop-shadow">{{ title }}</h1>
          <p v-if="subtitle" class="mt-2 text-white/90">{{ subtitle }}</p>
          <div class="mt-4"><slot /></div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import EBImage from '@/components/media/EBImage.vue'

const props = withDefaults(defineProps<{ src: string; alt: string; title?: string; subtitle?: string; darkSrc?: string; priority?: boolean; overlay?: 'orange' | 'teal' | 'mixed' }>(), {
  overlay: 'mixed',
  priority: false,
})

const overlayClass = computed(() => {
  if (props.overlay === 'orange') return 'bg-gradient-to-t from-[#0b0f16]/70 via-transparent to-transparent'
  if (props.overlay === 'teal') return 'bg-gradient-to-t from-black/60 via-black/20 to-transparent'
  return 'bg-gradient-to-t from-black/60 via-black/20 to-transparent'
})
</script>
