<template>
  <header class="fixed top-0 inset-x-0 z-40 backdrop-blur bg-[color:var(--bg)]/80 border-b border-black/5 dark:border-white/10">
    <div class="container-px mx-auto">
      <div class="flex items-center justify-between h-16">
        <RouterLink to="/" class="flex items-center gap-3">
          <img src="/logo-icon.png" alt="ExploreBenin360" class="h-8 w-8" />
          <div class="hidden sm:flex flex-col leading-tight">
            <span class="text-xl font-bold text-[color:var(--color-primary)]">ExploreBenin<span class="text-[color:var(--color-secondary)]">360</span></span>
            <span class="text-xs text-[color:var(--color-text-muted)]">{{ t('brand.baseline') }}</span>
          </div>
        </RouterLink>
        <nav class="hidden md:flex items-center gap-6">
          <RouterLink v-for="item in menu" :key="item.to" :to="item.to" class="text-sm font-medium text-[color:var(--color-text)]/90 hover:text-[color:var(--color-secondary)] focus-ring">
            {{ t(item.label) }}
          </RouterLink>
          <RouterLink to="/offerings" class="text-sm font-medium text-[color:var(--color-text)]/90 hover:text-[color:var(--color-secondary)] focus-ring">{{ t('nav.offerings') }}</RouterLink>
        </nav>
        <div class="flex items-center gap-2">
          <select :value="locale" @change="setLocale($event.target.value)" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
            <option value="fr">FR</option>
            <option value="en">EN</option>
          </select>
          <button @click="toggleDark()" class="btn-base focus-ring h-9 w-9 rounded-full" :aria-label="isDark ? t('common.light_mode') : t('common.dark_mode')">
            <Icon :name="isDark ? 'Sun' : 'Moon'" />
          </button>
          <RouterLink to="/dashboard/favorites" class="btn-base focus-ring h-9 w-9 rounded-full inline-flex items-center justify-center" :aria-label="t('nav.favorites')">
            <Icon name="Heart" />
          </RouterLink>
          <template v-if="!isAuthenticated">
            <RouterLink to="/login" class="hidden sm:inline-flex btn-base focus-ring h-9 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
              {{ t('nav.login') }}
            </RouterLink>
          </template>
          <template v-else>
            <RouterLink to="/dashboard/reservations" class="hidden sm:inline-flex btn-base focus-ring h-9 px-3 rounded-md">
              {{ t('nav.my_reservations') }}
            </RouterLink>
            <button @click="logout" class="hidden sm:inline-flex btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">
              {{ t('nav.logout') }}
            </button>
          </template>
        </div>
      </div>
    </div>
  </header>
  <div class="h-16"></div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import Icon from '@/components/ui/Icon.vue'
import { useAuthStore } from '@/stores/auth'

const { t, locale } = useI18n()
const auth = useAuthStore()
const isAuthenticated = computed(() => auth.isAuthenticated)
const logout = () => auth.logout()

const menu = [
  { to: '/', label: 'nav.home' },
  { to: '/destinations', label: 'nav.destinations' },
  { to: '/experiences', label: 'nav.experiences' },
  { to: '/hebergements', label: 'nav.hebergements' },
  { to: '/guides', label: 'nav.guides' },
  { to: '/blog', label: 'nav.blog' },
  { to: '/contact', label: 'nav.contact' }
]

const isDark = computed(() => document.documentElement.classList.contains('dark'))

function setLocale(val) {
  locale.value = val
  localStorage.setItem('eb360:locale', val)
  if (typeof document !== 'undefined') document.documentElement.setAttribute('lang', val)
}

function toggleDark() {
  const el = document.documentElement
  el.classList.toggle('dark')
  localStorage.setItem('eb360:theme', el.classList.contains('dark') ? 'dark' : 'light')
}
</script>
