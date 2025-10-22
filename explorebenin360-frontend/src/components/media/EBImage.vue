<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref } from 'vue'
import { buildCloudinaryUrl } from '@/utils/media'

interface Props {
  src: string
  alt: string
  provider?: 'cloudinary' | 's3'
  width?: number
  height?: number
  maxWidth?: number
  sizes?: string
  aspectRatio?: string
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  provider: (import.meta.env.VITE_MEDIA_PROVIDER as 'cloudinary' | 's3') || 'cloudinary',
  maxWidth: Number(import.meta.env.VITE_MEDIA_MAX_WIDTH || 1600),
  sizes: '(max-width: 768px) 100vw, 50vw',
})

const isVisible = ref(false)
const el = ref<HTMLElement | null>(null)

onMounted(() => {
  const io = new IntersectionObserver((entries) => {
    if (entries.some(e => e.isIntersecting)) {
      isVisible.value = true
      io.disconnect()
    }
  }, { rootMargin: '200px' })
  if (el.value) io.observe(el.value)
})

onBeforeUnmount(() => {})

const srcset = computed(() => {
  const widths = [400, 800, 1200, Math.min(1600, props.maxWidth || 1600)]
  if (!isVisible.value) return undefined
  if (props.provider === 'cloudinary') {
    return widths.map(w => `${buildCloudinaryUrl(props.src, { width: w, quality: 'auto', format: 'auto' })} ${w}w`).join(', ')
  }
  return widths.map(w => `${props.src} ${w}w`).join(', ')
})

const baseSrc = computed(() => {
  if (!isVisible.value) return 'data:image/gif;base64,R0lGODlhAQABAAAAACw='
  if (props.provider === 'cloudinary') {
    return buildCloudinaryUrl(props.src, { width: 800, quality: 'auto', format: 'auto' })
  }
  return props.src
})
</script>

<template>
  <figure :style="aspectRatio ? { aspectRatio } : undefined" ref="el">
    <img
      :src="baseSrc"
      :alt="alt"
      :width="width"
      :height="height"
      :srcset="srcset"
      :sizes="sizes"
      loading="lazy"
      decoding="async"
      @error="($event.target as HTMLImageElement).src = baseSrc"
      :class="[$props.class, 'rounded-md bg-[#0b0f16] object-cover']"
    />
    <figcaption v-if="$slots.default" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
      <slot />
    </figcaption>
  </figure>
</template>

<style scoped>
figure { display: block; }
</style>
