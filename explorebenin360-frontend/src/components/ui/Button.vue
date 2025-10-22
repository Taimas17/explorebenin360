<template>
  <button :class="classes" :disabled="disabled">
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'primary' },
  size: { type: String, default: 'md' },
  full: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
})

const classes = computed(() => {
  const base = 'btn-base focus-ring rounded-md'
  const sizes = {
    sm: 'h-9 px-3 text-sm',
    md: 'h-10 px-4 text-sm',
    lg: 'h-12 px-6 text-base',
  }[props.size]
  const variants = {
    primary: 'text-white bg-[color:var(--color-primary)] hover:bg-[color:var(--color-primary-hover)] shadow-[var(--shadow-md)]',
    secondary: 'text-white bg-[color:var(--color-secondary)] hover:bg-[color:var(--color-secondary-hover)] shadow-[var(--shadow-md)]',
    outline: 'border border-black/10 dark:border-white/10 text-[color:var(--color-text)] hover:bg-black/5 dark:hover:bg-white/5',
  }[props.variant]
  const width = props.full ? 'w-full' : ''
  const disabled = props.disabled ? 'opacity-60 cursor-not-allowed' : ''
  return [base, sizes, variants, width, disabled].join(' ')
})
</script>
