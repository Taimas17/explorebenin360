<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Admin" :title="t('admin.dashboard')" class="mb-6" />

    <div class="grid md:grid-cols-4 gap-3 mb-6">
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ t('admin.kpi_users') }}</div>
        <div class="text-xl font-semibold">12,450</div>
      </div>
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ t('admin.kpi_providers_pending') }}</div>
        <div class="text-xl font-semibold">3</div>
      </div>
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ t('admin.kpi_bookings') }}</div>
        <div class="text-xl font-semibold">1,125</div>
      </div>
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ t('admin.kpi_revenue') }}</div>
        <div class="text-xl font-semibold">XOF 85,200,000</div>
      </div>
    </div>

    <div class="rounded-md border border-black/10 dark:border-white/10 p-4 mb-6">
      <div class="text-sm font-medium mb-2">{{ t('admin.bookings_over_time') }}</div>
      <SmallAreaChart :data="series" color="#16a34a" />
    </div>

    <div class="grid md:grid-cols-2 gap-3 mb-6">
      <RouterLink to="/admin/users" class="rounded-md border border-black/10 dark:border-white/10 p-4 hover:bg-black/5 dark:hover:bg-white/5">
        <div class="text-sm font-medium mb-1">Gérer les utilisateurs</div>
        <div class="text-xs text-[color:var(--color-text-muted)]">Lister, modifier, suspendre et gérer les rôles</div>
      </RouterLink>
      <RouterLink to="/admin/accommodations" class="rounded-md border border-black/10 dark:border-white/10 p-4 hover:bg-black/5 dark:hover:bg-white/5">
        <div class="text-sm font-medium mb-1">Gérer les hébergements</div>
        <div class="text-xs text-[color:var(--color-text-muted)]">Lister, créer, éditer et supprimer</div>
      </RouterLink>
      <RouterLink to="/admin/articles" class="rounded-md border border-black/10 dark:border-white/10 p-4 hover:bg-black/5 dark:hover:bg-white/5">
        <div class="text-sm font-medium mb-1">Gérer les articles</div>
        <div class="text-xs text-[color:var(--color-text-muted)]">Blog et actualités</div>
      </RouterLink>
      <RouterLink to="/admin/events" class="rounded-md border border-black/10 dark:border-white/10 p-4 hover:bg-black/5 dark:hover:bg-white/5">
        <div class="text-sm font-medium mb-1">Gérer les événements</div>
        <div class="text-xs text-[color:var(--color-text-muted)]">Agenda et manifestations</div>
      </RouterLink>
      <RouterLink to="/admin/guides" class="rounded-md border border-black/10 dark:border-white/10 p-4 hover:bg-black/5 dark:hover:bg-white/5">
        <div class="text-sm font-medium mb-1">Gérer les guides</div>
        <div class="text-xs text-[color:var(--color-text-muted)]">Annuaire des guides</div>
      </RouterLink>
      <RouterLink to="/admin/places" class="rounded-md border border-black/10 dark:border-white/10 p-4 hover:bg-black/5 dark:hover:bg-white/5">
        <div class="text-sm font-medium mb-1">Gérer les destinations</div>
        <div class="text-xs text-[color:var(--color-text-muted)]">Lieux et attractions</div>
      </RouterLink>
    </div>

    <div>
      <div class="text-sm font-medium mb-2">{{ t('admin.recent_activity') }}</div>
      <ul class="rounded-md border border-black/10 dark:border-white/10 divide-y divide-black/10 dark:divide-white/10">
        <li v-for="a in activity" :key="a.id" class="px-3 py-2 text-sm">{{ a.text }}</li>
      </ul>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import SmallAreaChart from '@/components/charts/SmallAreaChart.vue'
import { buildTimeseries } from '@/lib/services/analytics'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const series = buildTimeseries(30, 7)
const activity = ref([
  { id: 1, text: 'Booking #1125 confirmed' },
  { id: 2, text: 'Provider “Agence Bénin Aventures” submitted KYC' },
  { id: 3, text: 'Article “Voyager à Ouidah” reported' },
])
</script>
