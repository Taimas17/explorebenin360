<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Explorer" :title="t('nav.explorer')" class="mb-6" />
    <MapShell />
  </div>
</template>
<script setup>
import { useI18n } from 'vue-i18n'
import MapShell from '@/components/maps/MapShell.vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
const { t } = useI18n()
const banner = '/src/assets/brand/images/destinations/banner-default.png'
</script>
