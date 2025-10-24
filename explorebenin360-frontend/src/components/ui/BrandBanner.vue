<template>
  <section class="relative w-full overflow-hidden rounded-[var(--radius-lg)]">
    <EBImage :src="currentSrc" :alt="alt || title || 'ExploreBenin360'" :width="1600" :height="900" aspect-ratio="16 / 9" class="w-full h-[36vh] md:h-[44vh] object-cover" :priority="priority" :sizes="'100vw'" />
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
import { computed, onMounted, onBeforeUnmount, ref } from 'vue'
import EBImage from '@/components/media/EBImage.vue'

const props = withDefaults(defineProps<{ src: string; alt: string; title?: string; subtitle?: string; darkSrc?: string; priority?: boolean; overlay?: 'orange' | 'teal' | 'mixed'; overlayStrength?: 'soft' | 'medium' | 'strong' }>(), {
  overlay: 'mixed',
  overlayStrength: 'medium',
  priority: false,
})

const isDark = ref(false)
let mq: MediaQueryList | null = null
const updateDark = () => {
  isDark.value = !!(mq?.matches) || document.documentElement.classList.contains('dark')
}

onMounted(() => {
  mq = window.matchMedia('(prefers-color-scheme: dark)')
  mq.addEventListener?.('change', updateDark)
  updateDark()
})

onBeforeUnmount(() => {
  mq?.removeEventListener?.('change', updateDark)
})

const currentSrc = computed(() => (props.darkSrc && isDark.value) ? props.darkSrc : props.src)

const strengthMap: Record<'soft' | 'medium' | 'strong', string> = {
  soft: 'from-black/50 via-black/20 dark:from-black/60 dark:via-black/30',
  medium: 'from-black/65 via-black/30 dark:from-black/70 dark:via-black/40',
  strong: 'from-black/80 via-black/50 dark:from-black/85 dark:via-black/60',
}

const tintMap: Record<'orange' | 'teal' | 'mixed', string> = {
  orange: 'via-[#FF6B35]/12 dark:via-black/40',
  teal: 'via-[#00796B]/14 dark:via-black/40',
  mixed: 'via-black/30 dark:via-black/40',
}

const overlayClass = computed(() => {
  return ['bg-gradient-to-t', strengthMap[props.overlayStrength || 'medium'], tintMap[props.overlay || 'mixed'], 'to-transparent'].join(' ')
})
</script>
