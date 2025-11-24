<template>
  <div class="container-px mx-auto py-8" v-if="item">
    <Alert v-if="$route.query.guard==='access_denied'" type="error" class="mb-4">{{ t('guards.access_denied') }}</Alert>
    <h1 class="text-2xl font-bold mb-2">{{ t('dashboard.reservation_detail') }}</h1>
    <div class="border border-black/10 dark:border-white/10 rounded-md p-4">
      <div class="font-medium mb-1">{{ item.offering.title }}</div>
      <Alert v-if="$route.query.reason==='login_required'" type="info" class="mb-2">{{ t('guards.login_required') }}</Alert>
      <div class="text-sm text-[color:var(--color-text-muted)] mb-2">{{ item.start_date }}<template v-if="item.end_date"> - {{ item.end_date }}</template> • {{ item.guests }} {{ t('checkout.guests') }}</div>
      <div class="text-sm mb-2">{{ t('dashboard.amount') }}: {{ item.currency }} {{ item.amount }}</div>
      <div class="text-sm mb-4 flex items-center gap-2">{{ t('dashboard.status') }}: <StatusBadge :status="item.status" /></div>
      <div class="flex flex-wrap gap-2">
        <a v-if="item.receipt_url" :href="item.receipt_url" target="_blank" rel="noopener" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.view_receipt') }}</a>
        <button v-if="item.status==='pending' || item.status==='authorized'" @click="cancel" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.cancel') }}</button>
        <button @click="openContact" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ t('common.contact_provider') || 'Contacter le provider' }}</button>
      </div>
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
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { fetchBookingService, cancelBookingService } from '@/lib/services/bookings'
import { createThread } from '@/lib/services/messages'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import Alert from '@/components/ui/Alert.vue'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const item = ref<any>(null)
const modals = ref({ contact: false })
const form = ref({ subject: '', initial_message: '' })
const loading = ref(false)

onMounted(async () => {
  item.value = await fetchBookingService(Number(route.params.id))
})

const cancel = async () => {
  if (!item.value) return
  await cancelBookingService(item.value.id)
  item.value = await fetchBookingService(item.value.id)
}

function openContact() {
  form.value.subject = item.value?.offering?.title ? `${t('common.about') || 'À propos'}: ${item.value.offering.title}` : ''
  form.value.initial_message = ''
  modals.value.contact = true
}

async function submitContact() {
  if (!item.value?.offering?.provider?.id) return
  loading.value = true
  try {
    await createThread({ subject: form.value.subject, provider_id: item.value.offering.provider.id, booking_id: item.value.id, initial_message: form.value.initial_message })
    modals.value.contact = false
    router.push('/dashboard/messages')
  } finally { loading.value = false }
}
</script>
