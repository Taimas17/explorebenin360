<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" :title="t('admin.providers')" class="mb-6" />

    <div class="mb-4 flex gap-2">
      <button
        v-for="status in ['pending', 'approved', 'rejected', 'suspended']"
        :key="status"
        @click="statusFilter = status; loadProviders()"
        :class="[
          'btn-base h-9 px-4 rounded border border-black/10 dark:border-white/10',
          statusFilter === status && 'bg-blue-500 text-white border-blue-500'
        ]"
      >
        {{ t(`admin.status_${status}`) }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-8">
      <Loader />
    </div>

    <EmptyState 
      v-else-if="providers.length === 0"
      variant="default"
      :title="t('admin.no_providers')"
    />

    <div v-else class="space-y-3">
      <div
        v-for="provider in providers"
        :key="provider.id"
        class="border border-black/10 dark:border-white/10 rounded-lg p-4"
      >
        <div class="flex items-start justify-between mb-3">
          <div>
            <h3 class="font-bold">{{ provider.business_name || provider.name }}</h3>
            <div class="text-sm text-[color:var(--color-text-muted)]">
              {{ provider.email }} · {{ provider.phone }}
            </div>
          </div>
          <Badge :variant="statusVariant(provider.provider_status)">
            {{ t(`admin.status_${provider.provider_status}`) }}
          </Badge>
        </div>

        <div class="text-sm mb-3">{{ provider.bio }}</div>

        <div class="flex items-center gap-2 mb-3">
          <Badge v-if="provider.kyc_submitted" variant="success">
            {{ t('admin.kyc_submitted') }}
          </Badge>
          <Badge v-if="provider.kyc_verified" variant="success">
            {{ t('admin.kyc_verified') }}
          </Badge>
        </div>

        <!-- Documents KYC -->
        <div v-if="provider.kyc_documents && provider.kyc_documents.length > 0" class="mb-3">
          <div class="text-xs font-medium mb-1">{{ t('admin.kyc_documents') }}:</div>
          <div class="flex gap-2 flex-wrap">
            <a
              v-for="(doc, idx) in provider.kyc_documents"
              :key="idx"
              :href="doc"
              target="_blank"
              class="text-xs underline text-blue-500 hover:text-blue-700"
            >
              {{ t('admin.document') }} {{ idx + 1 }}
            </a>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
          <template v-if="provider.provider_status === 'pending'">
            <button
              @click="handleApprove(provider.id)"
              class="btn-base h-9 px-4 rounded bg-green-500 text-white hover:bg-green-600"
            >
              {{ t('admin.approve') }}
            </button>
            <button
              @click="handleReject(provider.id)"
              class="btn-base h-9 px-4 rounded bg-red-500 text-white hover:bg-red-600"
            >
              {{ t('admin.reject') }}
            </button>
          </template>

          <template v-if="provider.provider_status === 'approved'">
            <button
              @click="handleSuspend(provider.id)"
              class="btn-base h-9 px-4 rounded border border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"
            >
              {{ t('admin.suspend') }}
            </button>
          </template>

          <template v-if="provider.provider_status === 'suspended'">
            <button
              @click="handleReactivate(provider.id)"
              class="btn-base h-9 px-4 rounded bg-blue-500 text-white hover:bg-blue-600"
            >
              {{ t('admin.reactivate') }}
            </button>
          </template>

          <RouterLink
            :to="`/admin/providers/${provider.id}`"
            class="btn-base h-9 px-4 rounded border border-black/10 dark:border-white/10"
          >
            {{ t('admin.view_details') }}
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { RouterLink } from 'vue-router'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import Badge from '@/components/ui/Badge.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import Loader from '@/components/ui/Loader.vue'
import { 
  fetchProviders, 
  approveProvider, 
  rejectProvider,
  suspendProvider,
  reactivateProvider 
} from '@/lib/api'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'

const loading = ref(false)
const providers = ref<any[]>([])
const statusFilter = ref('pending')

onMounted(() => loadProviders())

async function loadProviders() {
  loading.value = true
  try {
    const res = await fetchProviders(statusFilter.value)
    providers.value = res.data || res // depending on backend response
  } finally {
    loading.value = false
  }
}

async function handleApprove(id: number) {
  if (!confirm(t('admin.confirm_approve'))) return
  
  try {
    await approveProvider(id)
    alert(t('admin.provider_approved'))
    loadProviders()
  } catch (error) {
    alert(t('admin.action_error'))
  }
}

async function handleReject(id: number) {
  const reason = prompt(t('admin.rejection_reason'))
  if (!reason) return
  
  try {
    await rejectProvider(id, reason)
    alert(t('admin.provider_rejected'))
    loadProviders()
  } catch (error) {
    alert(t('admin.action_error'))
  }
}

async function handleSuspend(id: number) {
  const reason = prompt(t('admin.suspension_reason'))
  if (!reason) return
  
  try {
    await suspendProvider(id, reason)
    alert(t('admin.provider_suspended'))
    loadProviders()
  } catch (error) {
    alert(t('admin.action_error'))
  }
}

async function handleReactivate(id: number) {
  if (!confirm(t('admin.confirm_reactivate'))) return
  
  try {
    await reactivateProvider(id)
    alert(t('admin.provider_reactivated'))
    loadProviders()
  } catch (error) {
    alert(t('admin.action_error'))
  }
}

function statusVariant(status: string) {
  const variants: Record<string, string> = {
    pending: 'warning',
    approved: 'success',
    rejected: 'error',
    suspended: 'error',
  }
  return variants[status] || 'default'
}
</script>
