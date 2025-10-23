<template>
  <div class="container-px mx-auto py-8">
    <h1 class="text-3xl font-bold mb-4">{{ t('offerings.title') }}</h1>
    <div class="grid md:grid-cols-3 gap-4">
      <div v-for="o in items" :key="o.id" class="border border-black/10 dark:border-white/10 rounded-lg p-4">
        <h3 class="font-semibold text-lg mb-1">
          <RouterLink :to="{ name: 'offering-detail', params: { slug: o.slug } }">{{ o.title }}</RouterLink>
        </h3>
        <p class="text-sm text-[color:var(--color-text-muted)] mb-2">{{ o.type }} • {{ o.currency }} {{ o.price }}</p>
        <p class="text-sm line-clamp-2">{{ o.description }}</p>
        <RouterLink :to="{ name: 'checkout', params: { slug: o.slug } }" class="mt-3 inline-flex btn-base focus-ring h-9 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
          {{ t('checkout.book_now') }}
        </RouterLink>
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { fetchOfferings } from '@/lib/api'

const { t } = useI18n()
const items = ref([])

onMounted(async () => {
  const res = await fetchOfferings()
  items.value = res.data
})
</script>
