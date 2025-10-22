<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import EBImage from './EBImage.vue'

interface Item {
  src: string
  alt: string
  caption?: string
  width?: number
  height?: number
}

const props = withDefaults(defineProps<{ items: Item[]; variant?: 'grid' | 'hero'; autoplayMs?: number }>(), {
  variant: 'grid',
  autoplayMs: 6000,
})

const isOpen = ref(false)
const activeIndex = ref(0)
const lightboxEl = ref<HTMLDivElement | null>(null)
let timer: any

function open(index: number) {
  activeIndex.value = index
  isOpen.value = true
  document.body.style.overflow = 'hidden'
}
function close() {
  isOpen.value = false
  document.body.style.overflow = ''
}
function next() { activeIndex.value = (activeIndex.value + 1) % props.items.length }
function prev() { activeIndex.value = (activeIndex.value + props.items.length - 1) % props.items.length }

function onKey(e: KeyboardEvent) {
  if (!isOpen.value) return
  if (e.key === 'Escape') close()
  if (e.key === 'ArrowRight') next()
  if (e.key === 'ArrowLeft') prev()
}

onMounted(() => {
  window.addEventListener('keydown', onKey)
  if (props.variant === 'hero') {
    timer = setInterval(() => next(), props.autoplayMs)
  }
})
onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKey)
  if (timer) clearInterval(timer)
})
</script>

<template>
  <div v-if="props.variant === 'grid'" class="grid grid-cols-2 md:grid-cols-3 gap-3">
    <button
      v-for="(it, i) in items"
      :key="i"
      type="button"
      class="group relative"
      @click="open(i)"
    >
      <EBImage :src="it.src" :alt="it.alt" :width="it.width" :height="it.height" class="w-full h-40" />
      <span v-if="it.caption" class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 opacity-0 group-hover:opacity-100 transition">
        {{ it.caption }}
      </span>
    </button>
  </div>

  <div v-else class="relative h-[56vh] md:h-[64vh] w-full overflow-hidden rounded-[var(--radius-lg)]">
    <EBImage
      v-for="(it, i) in items"
      :key="i"
      :src="it.src"
      :alt="it.alt"
      :width="1600"
      :height="900"
      aspect-ratio="16 / 9"
      class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
      :class="i === activeIndex ? 'opacity-100' : 'opacity-0'"
      :priority="i === 0"
    />
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent pointer-events-none"></div>
    <div class="relative z-10 h-full flex items-end">
      <div class="container-px mx-auto pb-8">
        <slot name="hero-content" />
      </div>
    </div>
    <div class="absolute inset-x-0 bottom-3 flex justify-center gap-2">
      <button v-for="(it,i) in items" :key="'d'+i" class="w-2.5 h-2.5 rounded-full" :style="{ background: i===activeIndex ? 'var(--color-accent)' : 'rgba(255,255,255,.6)' }" @click="activeIndex=i" aria-label="Slide {{ i+1 }}" />
    </div>
  </div>

  <teleport to="body">
    <div v-if="isOpen" ref="lightboxEl" class="fixed inset-0 z-50 bg-black/90 text-white flex items-center justify-center p-4" role="dialog" aria-modal="true">
      <button class="absolute top-4 right-4 p-2 bg-white/10 rounded" @click="close" aria-label="Close">✕</button>
      <button class="absolute left-4 p-2 bg-white/10 rounded" @click="prev" aria-label="Previous">‹</button>
      <figure class="max-w-[90vw] max-h-[80vh]">
        <img :src="items[activeIndex].src" :alt="items[activeIndex].alt" class="object-contain max-w-full max-h-[80vh]" />
        <figcaption v-if="items[activeIndex].caption" class="mt-2 text-sm text-gray-300 text-center">{{ items[activeIndex].caption }}</figcaption>
      </figure>
      <button class="absolute right-4 p-2 bg-white/10 rounded" @click="next" aria-label="Next">›</button>
    </div>
  </teleport>
</template>
