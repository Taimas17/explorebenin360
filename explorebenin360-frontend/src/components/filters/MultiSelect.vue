<template>
  <div>
    <label v-if="label" class="block text-sm font-medium mb-1">{{ label }}</label>
    <div class="relative">
      <button type="button" class="w-full rounded-lg border px-3 py-2 text-left" @click="open = !open" :aria-expanded="open">
        <span v-if="modelValue?.length">{{ modelValue.join(', ') }}</span>
        <span v-else class="text-[color:var(--color-text-muted)]">{{ placeholder }}</span>
      </button>
      <div v-if="open" class="absolute z-10 mt-1 w-full rounded-lg border bg-white shadow-lg max-h-60 overflow-auto">
        <div v-for="opt in options" :key="opt.value" class="px-3 py-2 hover:bg-black/5 flex items-center gap-2">
          <input type="checkbox" :value="opt.value" :checked="internal.has(opt.value)" @change="toggle(opt.value)" :id="id + '-' + opt.value" />
          <label :for="id + '-' + opt.value" class="cursor-pointer">{{ opt.label }}</label>
        </div>
      </div>
    </div>
    <button v-if="modelValue?.length" class="mt-2 text-sm text-blue-600" @click="$emit('update:modelValue', [])">{{ clearText }}</button>
  </div>
</template>
<script setup>
import { computed, ref, watch } from 'vue'
const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  options: { type: Array, default: () => [] },
  label: { type: String, default: '' },
  placeholder: { type: String, default: 'Sélectionner' },
  clearText: { type: String, default: 'Réinitialiser' },
  id: { type: String, default: 'multiselect' },
})
const emit = defineEmits(['update:modelValue'])
const open = ref(false)
const internal = ref(new Set(props.modelValue))
watch(() => props.modelValue, v => internal.value = new Set(v))
const toggle = (val) => {
  if (internal.value.has(val)) internal.value.delete(val); else internal.value.add(val)
  emit('update:modelValue', Array.from(internal.value))
}
</script>
<style scoped>
</style>
