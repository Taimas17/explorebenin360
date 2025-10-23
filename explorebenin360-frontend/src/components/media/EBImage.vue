<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { buildCloudinaryUrl, buildWidthSrcset, buildDprSrcset, buildCloudinaryLqip } from '@/utils/media'

defineOptions({ inheritAttrs: false })

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
  lqip?: boolean
  priority?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  provider: (import.meta.env.VITE_MEDIA_PROVIDER as 'cloudinary' | 's3') || 'cloudinary',
  maxWidth: Number(import.meta.env.VITE_MEDIA_MAX_WIDTH || 1600),
  sizes: '(max-width: 768px) 100vw, 50vw',
  lqip: true,
  priority: false,
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

const widthBasedSrcset = computed(() => {
  if (!isVisible.value) return undefined
  return buildWidthSrcset(props.src, props.provider)
})

const dprSrcset = computed(() => {
  if (!props.width) return undefined
  if (!isVisible.value) return undefined
  return buildDprSrcset(props.src, props.width, props.provider)
})

const baseSrc = computed(() => {
  if (!isVisible.value && props.lqip && props.provider === 'cloudinary') {
    return buildCloudinaryLqip(props.src)
  }
  if (props.provider === 'cloudinary') {
    return buildCloudinaryUrl(props.src, { width: Math.min(800, props.maxWidth || 800), quality: 'auto', format: 'auto' })
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
      :srcset="dprSrcset || widthBasedSrcset"
      :sizes="sizes"
      :loading="priority ? 'eager' : 'lazy'"
      :fetchpriority="priority ? 'high' : undefined"
      decoding="async"
      @error="($event.target as HTMLImageElement).src = baseSrc as string"
      v-bind="$attrs"
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
