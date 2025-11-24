<template>
  <div v-if="avatarUrl" class="inline-flex" :style="{ width: size+'px', height: size+'px' }">
    <img :src="avatarUrl" :alt="name || 'Avatar'" class="w-full h-full rounded-full object-cover" />
  </div>
  <div v-else class="inline-flex items-center justify-center rounded-full" :style="{ width: size+'px', height: size+'px', background: 'linear-gradient(135deg, var(--color-secondary), var(--color-accent))' }" aria-hidden="true">
    <svg v-if="icon" viewBox="0 0 24 24" class="w-1/2 h-1/2 text-white" fill="currentColor">
      <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z" />
    </svg>
    <span v-else class="text-white font-semibold">{{ initials }}</span>
  </div>
</template>
<script setup lang="ts">
const props = withDefaults(defineProps<{ user?: { name?: string; avatar_url?: string | null }; name?: string; avatarUrl?: string | null; size?: number; icon?: boolean }>(), {
  size: 40,
  icon: true,
})
const name = props.user?.name || props.name || 'EB'
const avatarUrl = props.user?.avatar_url ?? props.avatarUrl ?? null
const initials = (name).split(' ').map(s => s[0]).join('').slice(0,2).toUpperCase()
</script>
