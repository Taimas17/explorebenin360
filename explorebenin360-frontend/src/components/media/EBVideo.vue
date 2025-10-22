<script setup lang="ts">
interface Props {
  src: string
  poster?: string
  width?: number
  height?: number
  controls?: boolean
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  controls: true,
})
</script>

<template>
  <video
    :src="src"
    :poster="poster"
    :width="width"
    :height="height"
    :controls="controls"
    playsinline
    preload="metadata"
    class="w-full rounded-md bg-black"
  >
    Sorry, your browser doesn't support embedded videos.
  </video>
</template>
