<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps<{ src: string; alt?: string; autoRotate?: boolean }>()

const container = ref<HTMLDivElement | null>(null)
let viewer: any = null
let mounted = false

async function ensurePannellumLoaded() {
  if ((window as any).pannellum) return
  await Promise.all([
    import('https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.js'),
    new Promise<void>((resolve) => {
      const link = document.createElement('link')
      link.rel = 'stylesheet'
      link.href = 'https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css'
      link.onload = () => resolve()
      document.head.appendChild(link)
    }),
  ])
}

onMounted(async () => {
  mounted = true
  try {
    await ensurePannellumLoaded()
    if (!mounted || !container.value) return
    viewer = (window as any).pannellum.viewer(container.value, {
      type: 'equirectangular',
      panorama: props.src,
      autoLoad: true,
      autoRotate: props.autoRotate ? 2 : 0,
      showControls: true,
      compass: false,
    })
  } catch (e) {
    console.error('Failed to initialize 360 viewer', e)
  }
})

onBeforeUnmount(() => { mounted = false; viewer = null })
</script>

<template>
  <div>
    <div ref="container" class="w-full h-72 md:h-96 rounded-md overflow-hidden bg-black"></div>
    <p v-if="alt" class="sr-only">{{ alt }}</p>
  </div>
</template>
