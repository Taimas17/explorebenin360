<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Fournisseurs" :title="t('admin.providers')" class="mb-6" />

    <div class="flex items-center gap-2 mb-3">
      <label class="text-xs text-[color:var(--color-text-muted)]">{{ t('filters.status') }}</label>
      <select v-model="status" @change="load" class="text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring">
        <option value="">{{ t('filters.all') }}</option>
        <option value="pending">pending</option>
        <option value="approved">approved</option>
        <option value="rejected">rejected</option>
      </select>
    </div>

    <div class="rounded-md border border-black/10 dark:border-white/10 overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-black/5 dark:bg-white/5">
          <tr>
            <th class="text-left px-3 py-2">#</th>
            <th class="text-left px-3 py-2">{{ t('admin.provider') }}</th>
            <th class="text-left px-3 py-2">{{ t('admin.email') }}</th>
            <th class="text-left px-3 py-2">{{ t('admin.phone') }}</th>
            <th class="text-left px-3 py-2">{{ t('admin.kyc') }}</th>
            <th class="text-left px-3 py-2">{{ t('admin.status') }}</th>
            <th class="text-left px-3 py-2"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in items" :key="p.id" class="border-t border-black/10 dark:border-white/10">
            <td class="px-3 py-2">{{ p.id }}</td>
            <td class="px-3 py-2">{{ p.name }}</td>
            <td class="px-3 py-2">{{ p.email }}</td>
            <td class="px-3 py-2">{{ p.phone }}</td>
            <td class="px-3 py-2">{{ p.kyc_verified ? t('admin.kyc_verified') : (p.kyc_submitted ? t('admin.kyc_submitted') : t('admin.kyc_missing')) }}</td>
            <td class="px-3 py-2">{{ p.status }}</td>
            <td class="px-3 py-2 text-right">
              <div class="inline-flex gap-2">
                <button @click="approve(p.id)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('admin.approve') }}</button>
                <button @click="reject(p.id)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10">{{ t('admin.reject') }}</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import { listProviders, approveProvider, rejectProvider } from '@/lib/services/providers'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const items = ref<any[]>([])
const status = ref('')

onMounted(load)

async function load() { items.value = await listProviders({ status: status.value || undefined }) }
async function approve(id: number) { await approveProvider(id); await load() }
async function reject(id: number) { await rejectProvider(id); await load() }
</script>
