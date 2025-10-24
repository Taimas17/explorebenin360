<template>
  <div class="container-px mx-auto py-8" v-if="offering">
    <h1 class="text-2xl font-bold mb-2">{{ t('checkout.title') }}</h1>
    <p class="text-[color:var(--color-text-muted)] mb-4">{{ offering.title }} — {{ offering.currency }} {{ offering.price }}</p>
    <form class="space-y-4 max-w-md" @submit.prevent="submit">
      <div>
        <label class="block text-sm mb-1">{{ t('checkout.start_date') }}</label>
        <input type="date" v-model="startDate" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" required />
      </div>
      <div>
        <label class="block text-sm mb-1">{{ t('checkout.end_date') }} ({{ t('common.optional') }})</label>
        <input type="date" v-model="endDate" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm mb-1">{{ t('checkout.guests') }}</label>
        <input type="number" v-model.number="guests" min="1" :max="offering.capacity" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 px-3 py-2" />
      </div>
      <button :disabled="loading" class="btn-base focus-ring h-10 px-6 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
        <span v-if="!loading">{{ t('checkout.pay_with_paystack') }}</span>
        <span v-else>{{ t('common.loading') }}</span>
      </button>
    </form>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { fetchOffering, createCheckoutSession } from '@/lib/api'
import { setPageMeta } from '@/utils/meta'

const { t } = useI18n()
const route = useRoute()
const offering = ref(null)
const startDate = ref('')
const endDate = ref('')
const guests = ref(1)
const loading = ref(false)

onMounted(async () => {
  const res = await fetchOffering(route.params.slug)
  offering.value = res.data
  setPageMeta({ title: `${t('checkout.title')} — ExploreBenin360`, description: (offering.value?.description || '').slice(0,120), path: `/checkout/${offering.value?.slug}` })
})

const submit = async () => {
  if (!offering.value) return
  loading.value = true
  try {
    const body = { offering_id: offering.value.id, start_date: startDate.value, guests: guests.value }
    if (endDate.value) body.end_date = endDate.value
    const res = await createCheckoutSession(body)
    window.location.href = res.authorization_url
  } catch (e) {
    alert(t('errors.checkout_failed'))
  } finally {
    loading.value = false
  }
}
</script>
