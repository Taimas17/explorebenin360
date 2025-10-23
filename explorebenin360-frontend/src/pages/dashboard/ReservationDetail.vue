<template>
  <div class="container-px mx-auto py-8" v-if="item">
    <h1 class="text-2xl font-bold mb-2">{{ t('dashboard.reservation_detail') }}</h1>
    <div class="border border-black/10 dark:border-white/10 rounded-md p-4">
      <div class="font-medium mb-1">{{ item.offering.title }}</div>
      <div class="text-sm text-[color:var(--color-text-muted)] mb-2">{{ item.start_date }}<template v-if="item.end_date"> - {{ item.end_date }}</template> • {{ item.guests }} {{ t('checkout.guests') }}</div>
      <div class="text-sm mb-2">{{ t('dashboard.amount') }}: {{ item.currency }} {{ item.amount }}</div>
      <div class="text-sm mb-4">{{ t('dashboard.status') }}: <span class="font-medium">{{ item.status }}</span></div>
      <button v-if="item.status==='pending' || item.status==='authorized'" @click="cancel" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ t('dashboard.cancel') }}</button>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { fetchBooking, cancelBooking } from '@/lib/api'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const item = ref(null)

onMounted(async () => {
  const res = await fetchBooking(Number(route.params.id))
  item.value = res.data
})

const cancel = async () => {
  if (!item.value) return
  await cancelBooking(item.value.id)
  const res = await fetchBooking(item.value.id)
  item.value = res.data
}
</script>
