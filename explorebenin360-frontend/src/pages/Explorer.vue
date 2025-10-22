<template>
  <div class="container-px mx-auto py-8 space-y-6">
    <BrandBanner :src="banner" alt="Bannière Explorer" :title="t('nav.explorer')" class="mb-2" />

    <div v-if="results.length === 0" class="grid place-items-center py-12">
      <EmptyState variant="search" title="Aucun résultat" description="Ajustez vos filtres ou élargissez la zone.">
        <Button variant="primary" @click="clearFilters">Effacer les filtres</Button>
      </EmptyState>
    </div>
    <div v-else>
      <MapShell :markers="results" @marker-click="onMarkerClick" />
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import MapShell from '@/components/maps/MapShell.vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import Button from '@/components/ui/Button.vue'

const emit = defineEmits<{ (e: 'clear'): void }>()
const { t } = useI18n()
const banner = '/src/assets/brand/images/destinations/banner-default.png'

const results = ref<any[]>([])

const clearFilters = () => {
  emit('clear')
  results.value = []
}

const onMarkerClick = (m: any) => {
  // placeholder for future interaction
  console.log('marker', m)
}
</script>
