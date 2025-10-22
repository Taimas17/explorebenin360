<template>
  <footer class="mt-12 border-t border-black/5 dark:border-white/10 py-8">
    <div class="container-px mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-[color:var(--color-text-muted)]">
      <div class="flex items-center gap-3">
        <img src="/logo-icon.png" alt="ExploreBenin360" class="h-6 w-6" />
        <span class="font-semibold text-[color:var(--color-primary)]">ExploreBenin<span class="text-[color:var(--color-secondary)]">360</span></span>
      </div>
      <p class="opacity-80">{{ t('footer.tagline') }}</p>
      <p class="opacity-60">© {{ new Date().getFullYear() }} ExploreBenin360 — {{ t('footer.rights') }}</p>
    </div>
  </footer>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>
