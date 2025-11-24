<template>
  <div>
    <textarea class="w-full min-h-40 rounded-md border border-black/10 dark:border-white/10 bg-transparent p-3 focus-ring" :placeholder="placeholder" v-model="model" />
  </div>
</template>
<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ modelValue: string; placeholder?: string }>()
const emit = defineEmits<{ (e: 'update:modelValue', v: string): void }>()

const model = computed({
  get: () => props.modelValue,
  set: (v: string) => emit('update:modelValue', v)
})
</script>
