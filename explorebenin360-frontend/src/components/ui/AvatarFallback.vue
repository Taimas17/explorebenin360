<template>
  <div class="inline-flex items-center justify-center rounded-full" :style="{ width: size+'px', height: size+'px', background: 'linear-gradient(135deg, var(--color-secondary), var(--color-accent))' }" aria-hidden="true">
    <svg v-if="icon" viewBox="0 0 24 24" class="w-1/2 h-1/2 text-white" fill="currentColor">
      <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z" />
    </svg>
    <span v-else class="text-white font-semibold">{{ initials }}</span>
  </div>
</template>
<script setup lang="ts">
const props = withDefaults(defineProps<{ name?: string; size?: number; icon?: boolean }>(), {
  size: 40,
  icon: true,
})
const initials = (props.name || 'EB').split(' ').map(s => s[0]).join('').slice(0,2).toUpperCase()
</script>
