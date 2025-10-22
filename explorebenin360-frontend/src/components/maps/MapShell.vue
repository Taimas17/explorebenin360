<template>
  <div class="relative w-full h-[60vh] rounded-[var(--radius-lg)] overflow-hidden bg-black/5 dark:bg-white/5 ring-1 ring-black/5 dark:ring-white/10">
    <div class="absolute inset-0 grid place-items-center text-center p-6">
      <div>
        <p class="text-lg font-semibold">Map coming soon</p>
        <p class="mt-1 text-sm text-[color:var(--color-text-muted)]">API Key: {{ maskedKey }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
const key = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || ''
const maskedKey = key ? key.slice(0, 6) + '...' : 'not set'
</script>
