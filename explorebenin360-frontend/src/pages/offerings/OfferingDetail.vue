<template>
  <div class="container-px mx-auto py-8" v-if="item">
    <h1 class="text-3xl font-bold mb-2">{{ item.title }}</h1>
    <p class="text-[color:var(--color-text-muted)] mb-4">{{ item.type }} • {{ item.currency }} {{ item.price }} • {{ t('offerings.capacity') }} {{ item.capacity }}</p>
    <p class="mb-6">{{ item.description }}</p>
    <div class="flex gap-2">
      <RouterLink :to="{ name: 'checkout', params: { slug: item.slug } }" class="inline-flex btn-base focus-ring h-10 px-5 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
        {{ t('checkout.book_now') }}
      </RouterLink>
      <button @click="openContact" class="inline-flex btn-base focus-ring h-10 px-4 rounded-md border border-black/10 dark:border:white/10">{{ t('common.contact_provider') || 'Contacter le provider' }}</button>
    </div>

    <div v-if="modals.contact" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="modals.contact=false"></div>
      <div class="relative bg-[color:var(--bg)] rounded-lg shadow-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-3">{{ t('common.contact_provider') || 'Contacter le provider' }}</h3>
        <form @submit.prevent="submitContact" class="space-y-3">
          <div>
            <label class="block text-sm mb-1">{{ t('dashboard.subject') || 'Sujet' }}</label>
            <input v-model="form.subject" required maxlength="255" class="w-full rounded-md border border-black/10 dark:border:white/10 bg-transparent px-3 py-2 focus-ring" />
          </div>
          <div>
            <label class="block text-sm mb-1">{{ t('dashboard.message') || 'Message' }}</label>
            <textarea v-model="form.initial_message" required maxlength="5000" rows="4" class="w-full rounded-md border border-black/10 dark:border:white/10 bg-transparent px-3 py-2 focus-ring"></textarea>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border:white/10" @click="modals.contact=false">{{ t('common.cancel') }}</button>
            <button type="submit" class="btn-base h-9 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }" :disabled="loading">
              <span v-if="loading">{{ t('common.sending') || 'Envoi...' }}</span>
              <span v-else>{{ t('dashboard.send') }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { fetchOffering } from '@/lib/api'
import { setPageMeta } from '@/utils/meta'
import { createThread } from '@/lib/services/messages'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const item = ref(null)
const modals = ref({ contact: false })
const form = ref({ subject: '', initial_message: '' })
const loading = ref(false)

onMounted(async () => {
  const res = await fetchOffering(route.params.slug)
  item.value = res.data
  setPageMeta({ title: `${item.value.title} — ExploreBenin360`, description: (item.value.description || '').slice(0,160), path: `/offerings/${item.value.slug}` })
})

function openContact() {
  form.value.subject = item.value?.title ? `${t('common.about') || 'À propos'}: ${item.value.title}` : ''
  form.value.initial_message = ''
  modals.value.contact = true
}

async function submitContact() {
  if (!item.value?.provider?.id) return
  loading.value = true
  try {
    await createThread({ subject: form.value.subject, provider_id: item.value.provider.id, offering_id: item.value.id, initial_message: form.value.initial_message })
    modals.value.contact = false
    router.push('/dashboard/messages')
  } finally { loading.value = false }
}
</script>
