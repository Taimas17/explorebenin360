<template>
  <div :class="classes">
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ type: { type: String, default: 'info' } })

const classes = computed(() => ({
  info: 'rounded-md p-3 text-sm bg-[color:var(--color-accent)]/20 text-[color:var(--color-text)]',
  success: 'rounded-md p-3 text-sm bg-[color:var(--color-secondary)]/15 text-[color:var(--color-secondary)]',
  error: 'rounded-md p-3 text-sm bg-red-500/10 text-red-600 dark:text-red-400',
}[props.type]))
</script>
