<template>
  <div class="flex flex-wrap gap-2">
    <button v-for="t in options" :key="t" @click="toggle(t)" :class="['px-3 py-1 rounded-full border', selected.has(t) ? 'bg-blue-600 text-white border-blue-600' : 'bg-transparent']">
      {{ t }}
    </button>
    <button v-if="selected.size" class="text-sm text-blue-600" @click="clear">{{ clearText }}</button>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue'
const props = defineProps({ modelValue: { type: Array, default: () => [] }, options: { type: Array, default: () => [] }, clearText: { type: String, default: 'Réinitialiser' } })
const emit = defineEmits(['update:modelValue'])
const selected = ref(new Set(props.modelValue))
watch(() => props.modelValue, v => selected.value = new Set(v))
const toggle = (t) => { if (selected.value.has(t)) selected.value.delete(t); else selected.value.add(t); emit('update:modelValue', Array.from(selected.value)) }
const clear = () => emit('update:modelValue', [])
</script>
