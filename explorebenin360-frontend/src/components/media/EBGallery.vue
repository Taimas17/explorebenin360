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

const props = defineProps<{ items: Item[] }>()

const isOpen = ref(false)
const activeIndex = ref(0)
const lightboxEl = ref<HTMLDivElement | null>(null)

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

onMounted(() => window.addEventListener('keydown', onKey))
onBeforeUnmount(() => window.removeEventListener('keydown', onKey))
</script>

<template>
  <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
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
