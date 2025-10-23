<template>
  <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-[color:var(--bg)]/95 backdrop-blur border-t border-black/5 dark:border-white/10">
    <ul class="grid grid-cols-5">
      <li v-for="item in items" :key="item.to">
        <RouterLink :to="item.to" class="flex flex-col items-center justify-center gap-1 py-2 text-xs text-[color:var(--color-text-muted)] hover:text-[color:var(--color-secondary)]">
          <Icon :name="item.icon" />
          <span>{{ t(item.label) }}</span>
        </RouterLink>
      </li>
    </ul>
  </nav>
  <div class="h-14 md:hidden"></div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import Icon from '@/components/ui/Icon.vue'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

const items = [
  { to: '/', icon: 'Home', label: 'nav.home' },
  { to: '/explorer', icon: 'Map', label: 'nav.explorer' },
  { to: '/guides', icon: 'BookOpen', label: 'nav.guides' },
  { to: '/hebergements', icon: 'Building', label: 'nav.hebergements' },
  { to: '/profile', icon: 'User', label: 'nav.profile' }
]
</script>
