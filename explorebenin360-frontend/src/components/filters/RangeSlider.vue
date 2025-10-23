<template>
  <div>
    <label v-if="label" class="block text-sm font-medium mb-1">{{ label }}</label>
    <div class="flex items-center gap-3">
      <input type="number" class="w-24 rounded-md border px-2 py-1" :placeholder="minPlaceholder" :value="modelValue?.[0] ?? ''" @input="onMin($event.target.value)" />
      <span>–</span>
      <input type="number" class="w-24 rounded-md border px-2 py-1" :placeholder="maxPlaceholder" :value="modelValue?.[1] ?? ''" @input="onMax($event.target.value)" />
    </div>
    <button v-if="modelValue && (modelValue[0] || modelValue[1])" class="mt-2 text-sm text-blue-600" @click="$emit('update:modelValue', [null,null])">{{ clearText }}</button>
  </div>
</template>
<script setup>
const props = defineProps({
  modelValue: { type: Array, default: () => [null, null] },
  label: { type: String, default: '' },
  minPlaceholder: { type: String, default: 'Min' },
  maxPlaceholder: { type: String, default: 'Max' },
  clearText: { type: String, default: 'Réinitialiser' },
})
const emit = defineEmits(['update:modelValue'])
const onMin = (v) => emit('update:modelValue', [v ? Number(v) : null, props.modelValue?.[1] ?? null])
const onMax = (v) => emit('update:modelValue', [props.modelValue?.[0] ?? null, v ? Number(v) : null])
</script>
