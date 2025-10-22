<template>
  <div class="group relative overflow-hidden rounded-[var(--radius-lg)] bg-white/60 dark:bg-white/5 backdrop-blur shadow-[var(--shadow-md)] ring-1 ring-black/5 dark:ring-white/10 transition-transform duration-300 hover:-translate-y-0.5">
    <div class="aspect-[4/3] bg-black/5 dark:bg-white/5"></div>
    <div class="p-4">
      <h3 class="text-base font-semibold mb-1"><slot name="title" /></h3>
      <p class="text-sm text-[color:var(--color-text-muted)]"><slot /></p>
      <div class="mt-3"><slot name="actions" /></div>
    </div>
  </div>
</template>

<script setup>
</script>
