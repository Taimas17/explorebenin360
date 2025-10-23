<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Offres" :title="t('provider.offers')" class="mb-6" />

    <div class="flex items-center justify-between mb-4">
      <div class="text-sm text-[color:var(--color-text-muted)]">{{ t('provider.offers_subtitle') }}</div>
      <RouterLink to="/provider/offers/new" class="btn-base focus-ring h-9 px-3 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('provider.create_offer') }}</RouterLink>
    </div>

    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else>
      <EmptyState v-if="items.length === 0" variant="default" :title="t('provider.no_offers')">
        <RouterLink to="/provider/offers/new" class="btn-base focus-ring h-9 px-3 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('provider.create_offer') }}</RouterLink>
      </EmptyState>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
        <Card v-for="o in items" :key="o.id">
          <template #media>
            <EBImage :src="o.cover_image_url || thumbs.destination" :alt="o.title" :width="800" :height="600" aspect-ratio="4 / 3" />
          </template>
          <template #title>{{ o.title }}</template>
          <div class="text-sm">{{ o.currency }} {{ o.price }}</div>
          <template #actions>
            <div class="flex items-center gap-2">
              <RouterLink :to="`/provider/offers/${o.id}`" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('common.details') }}</RouterLink>
              <label class="inline-flex items-center gap-1 text-xs">
                <input type="checkbox" class="accent-[color:var(--color-secondary)]" :checked="o.status==='published'" @change="toggleStatus(o)"/>
                {{ o.status }}
              </label>
            </div>
          </template>
        </Card>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import Card from '@/components/ui/Card.vue'
import EBImage from '@/components/media/EBImage.vue'
import { providerOfferings, updateOffering } from '@/lib/services/offerings'

const { t } = useI18n()
const items = ref<any[]>([])
const loading = ref(false)
const banner = '/src/assets/brand/images/dashboard/provider/header.png'
const thumbs = { destination: '/src/assets/brand/images/thumbs/destination-thumb.png' }

onMounted(async () => {
  loading.value = true
  try { items.value = await providerOfferings() } finally { loading.value = false }
})

async function toggleStatus(o: any) {
  const next = o.status === 'published' ? 'draft' : 'published'
  o.status = next
  await updateOffering(o.id, { status: next })
}
</script>
