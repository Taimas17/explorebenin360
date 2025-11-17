<template>
  <div class="container-px mx-auto py-8 max-w-4xl">
    <BrandBanner :src="banner" alt="Bannière Offres" :title="t('provider.edit_offer')" class="mb-6" />

    <div v-if="!item" class="text-sm text-[color:var(--color-text-muted)]">{{ t('common.loading') }}</div>
    <div v-else class="grid md:grid-cols-2 gap-6">
      <form class="space-y-4" @submit.prevent="onSubmit">
        <div>
          <label class="block text-sm font-medium mb-1">{{ t('provider.offer_title') }}</label>
          <input v-model="item.title" required class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">{{ t('provider.offer_description') }}</label>
          <textarea v-model="item.description" rows="6" class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium mb-1">{{ t('provider.price') }}</label>
            <input v-model.number="item.price" type="number" min="0" class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">{{ t('provider.currency') }}</label>
            <input v-model="item.currency" class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">{{ t('provider.status') }}</label>
          <select v-model="item.status" class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring">
            <option value="draft">draft</option>
            <option value="published">published</option>
          </select>
        </div>
        <div class="flex justify-end gap-2">
          <RouterLink to="/provider/offers" class="btn-base focus-ring h-10 px-4 rounded-md border border-black/10 dark:border-white/10">{{ t('common.cancel') }}</RouterLink>
          <button class="btn-base focus-ring h-10 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('common.save') }}</button>
        </div>
      </form>

      <div>
        <h3 class="text-sm font-medium mb-2">{{ t('provider.gallery') }}</h3>
        <div class="grid grid-cols-2 gap-2 mb-3">
          <EBImage v-for="(img, idx) in item.gallery" :key="idx" :src="typeof img === 'string' ? img : img.src" :alt="item.title" :width="800" :height="600" aspect-ratio="4 / 3" />
        </div>
        <div class="text-xs text-[color:var(--color-text-muted)] mb-2">{{ t('provider.gallery_hint') }}</div>
        <input v-model="newImage" placeholder="https://..." class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring mb-2" />
        <div class="flex gap-2">
          <button type="button" @click="addImage" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('provider.add_image') }}</button>
          <button type="button" @click="clearGallery" class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('provider.clear_gallery') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import EBImage from '@/components/media/EBImage.vue'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import { fetchOfferingById, updateOffering } from '@/lib/services/offerings'

const { t } = useI18n()
const route = useRoute()
const banner = '/src/assets/brand/images/dashboard/provider/header.png'
const item = ref<any>(null)
const newImage = ref('')

onMounted(async () => {
  const id = Number(route.params.id)
  item.value = await fetchOfferingById(id)
})

async function onSubmit() {
  const payload: any = { ...item.value }
  if (Array.isArray(payload.gallery)) {
    payload.gallery = payload.gallery.map((g: any) => typeof g === 'string' ? g : g.src)
  }
  await updateOffering(item.value.id, payload)
  alert(t('provider.saved'))
}

function addImage() {
  const url = newImage.value.trim(); if (!url) return
  item.value.gallery = [...(item.value.gallery || []), url]
  newImage.value = ''
}

function clearGallery() { item.value.gallery = [] }
</script>
