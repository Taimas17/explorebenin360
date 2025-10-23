<template>
  <div class="relative w-full h-[60vh] rounded-[var(--radius-lg)] overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
    <component
      :is="providerComponent"
      v-bind="passProps"
      @marker-click="(id) => $emit('marker-click', id)"
      @bounds-change="(p) => $emit('bounds-change', p)"
      class="w-full h-full"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import LeafletMap from './LeafletMap.vue'

const props = defineProps({
  provider: { type: String, default: 'leaflet' },
  markers: { type: Array, default: () => [] },
  zoom: { type: Number, default: 6 },
  center: { type: Object, default: null },
  bbox: { type: Array, default: null },
  cluster: { type: Boolean, default: true },
  fitOnMarkersChange: { type: Boolean, default: true },
})

defineEmits(['marker-click','bounds-change'])

const providerComponent = computed(() => {
  switch (props.provider) {
    case 'leaflet': return LeafletMap
    case 'mapbox':
    case 'google':
    default:
      return LeafletMap
  }
})

const passProps = computed(() => ({
  markers: props.markers,
  zoom: props.zoom,
  center: props.center,
  bbox: props.bbox,
  cluster: props.cluster,
  fitOnMarkersChange: props.fitOnMarkersChange,
}))
</script>
