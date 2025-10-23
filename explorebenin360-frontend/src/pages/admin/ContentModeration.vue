<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Modération" :title="t('admin.moderation')" class="mb-6" />

    <div v-if="items.length === 0" class="grid place-items-center py-12">
      <EmptyState variant="default" :title="t('admin.nothing_to_moderate')" />
    </div>

    <div v-else class="space-y-3">
      <div v-for="it in items" :key="it.id" class="border border-black/10 dark:border-white/10 rounded-md p-4">
        <div class="text-sm text-[color:var(--color-text-muted)]">{{ it.type }}</div>
        <div class="font-medium">{{ it.title }}</div>
        <div class="text-xs text-[color:var(--color-text-muted)]">{{ it.reason }}</div>
        <div class="mt-2 flex gap-2">
          <button class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('admin.approve') }}</button>
          <button class="btn-base focus-ring h-9 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('admin.reject') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const items = ref<any[]>([])

onMounted(() => {
  items.value = []
})
</script>
