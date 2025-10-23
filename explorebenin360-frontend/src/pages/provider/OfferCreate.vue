<template>
  <div class="container-px mx-auto py-8 max-w-3xl">
    <BrandBanner :src="banner" alt="Bannière Offres" :title="t('provider.create_offer')" class="mb-6" />
    <form class="space-y-4" @submit.prevent="onSubmit">
      <div>
        <label class="block text-sm font-medium mb-1">{{ t('provider.offer_title') }}</label>
        <input v-model="form.title" required class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring" />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">{{ t('provider.offer_description') }}</label>
        <textarea v-model="form.description" rows="4" class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring"></textarea>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium mb-1">{{ t('provider.price') }}</label>
          <input v-model.number="form.price" type="number" min="0" class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">{{ t('provider.currency') }}</label>
          <input v-model="form.currency" class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring" />
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">{{ t('provider.cover_image_url') }}</label>
        <input v-model="form.cover_image_url" class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring" placeholder="https://..." />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">{{ t('provider.status') }}</label>
        <select v-model="form.status" class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring">
          <option value="draft">draft</option>
          <option value="published">published</option>
        </select>
      </div>
      <div class="flex justify-end gap-2">
        <RouterLink to="/provider/offers" class="btn-base focus-ring h-10 px-4 rounded-md border border-black/10 dark:border-white/10">{{ t('common.cancel') }}</RouterLink>
        <button class="btn-base focus-ring h-10 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('provider.create_offer') }}</button>
      </div>
    </form>
  </div>
</template>
<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import { createOffering } from '@/lib/services/offerings'

const { t } = useI18n()
const router = useRouter()
const banner = '/src/assets/brand/images/dashboard/provider/header.png'

const form = reactive<any>({ title: '', description: '', price: 0, currency: 'XOF', status: 'draft', cover_image_url: '' })

async function onSubmit() {
  const created = await createOffering(form)
  router.push(`/provider/offers/${created.id}`)
}
</script>
