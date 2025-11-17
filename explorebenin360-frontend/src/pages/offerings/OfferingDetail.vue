<template>
  <div class="container-px mx-auto py-8" v-if="item">
    <h1 class="text-3xl font-bold mb-2">{{ item.title }}</h1>
    <p class="text-[color:var(--color-text-muted)] mb-4">{{ item.type }} • {{ item.currency }} {{ item.price }} • {{ t('offerings.capacity') }} {{ item.capacity }}</p>
    <p class="mb-6">{{ item.description }}</p>

    <!-- Provider info -->
    <div v-if="item.provider" class="mt-6 p-4 rounded border border-black/10 dark:border-white/10">
      <div class="flex items-center gap-2 mb-1">
        <span class="font-medium">{{ item.provider.business_name || item.provider.name }}</span>
        <Badge v-if="item.provider.kyc_verified" variant="success" class="text-xs">
          <Icon name="CheckBadge" class="h-3 w-3 inline" />
          {{ t('provider.verified') }}
        </Badge>
      </div>
      <div class="text-sm text-[color:var(--color-text-muted)]">
        {{ item.provider.email }}
        <span v-if="item.provider.phone"> · {{ item.provider.phone }}</span>
      </div>
    </div>

    <RouterLink :to="{ name: 'checkout', params: { slug: item.slug } }" class="inline-flex btn-base focus-ring h-10 px-5 rounded-md text-white mt-6" :style="{ backgroundColor: 'var(--color-primary)' }">
      {{ t('checkout.book_now') }}
    </RouterLink>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { fetchOffering } from '@/lib/api'
import { setPageMeta } from '@/utils/meta'
import Badge from '@/components/ui/Badge.vue'
import Icon from '@/components/ui/Icon.vue'

const { t } = useI18n()
const route = useRoute()
const item = ref<any | null>(null)

onMounted(async () => {
  const res = await fetchOffering(route.params.slug as string)
  item.value = res.data
  setPageMeta({ title: `${item.value.title} — ExploreBenin360`, description: (item.value.description || '').slice(0,160), path: `/offerings/${item.value.slug}` })
})
</script>
