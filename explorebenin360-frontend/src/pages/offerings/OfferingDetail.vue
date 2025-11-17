<template>
  <div class="container-px mx-auto py-8" v-if="item">
    <h1 class="text-3xl font-bold mb-2">{{ item.title }}</h1>
    <p class="text-[color:var(--color-text-muted)] mb-4">{{ item.type }} • {{ item.currency }} {{ item.price }} • {{ t('offerings.capacity') }} {{ item.capacity }}</p>
    <p class="mb-6">{{ item.description }}</p>
    <RouterLink :to="{ name: 'checkout', params: { slug: item.slug } }" class="inline-flex btn-base focus-ring h-10 px-5 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
      {{ t('checkout.book_now') }}
    </RouterLink>
    <div class="mt-6">
      <ReportButton
        v-if="item"
        reportable-type="App\\Models\\Offering"
        :reportable-id="item.id"
      />
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { fetchOffering } from '@/lib/api'
import { setPageMeta } from '@/utils/meta'
import ReportButton from '@/components/ui/ReportButton.vue'

const { t } = useI18n()
const route = useRoute()
const item = ref(null)

onMounted(async () => {
  const res = await fetchOffering(route.params.slug)
  item.value = res.data
  setPageMeta({ title: `${item.value.title} — ExploreBenin360`, description: (item.value.description || '').slice(0,160), path: `/offerings/${item.value.slug}` })
})
</script>
