<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" :title="t('admin.moderation')" class="mb-6" />

    <div class="mb-4">
      <select v-model="statusFilter" @change="loadReports" class="rounded border border-black/10 dark:border-white/10 px-3 py-2">
        <option value="pending">{{ t('admin.pending') }}</option>
        <option value="reviewing">{{ t('admin.reviewing') }}</option>
        <option value="resolved">{{ t('admin.resolved') }}</option>
        <option value="dismissed">{{ t('admin.dismissed') }}</option>
      </select>
    </div>

    <div v-if="loading" class="text-center py-8">
      <Loader />
    </div>

    <EmptyState 
      v-else-if="reports.length === 0" 
      variant="default" 
      :title="t('admin.nothing_to_moderate')" 
    />

    <div v-else class="space-y-3">
      <div
        v-for="report in reports"
        :key="report.id"
        class="border border-black/10 dark:border-white/10 rounded-lg p-4"
      >
        <div class="flex items-start justify-between mb-2">
          <div>
            <Badge>{{ report.type }}</Badge>
            <h3 class="font-medium mt-1">{{ report.title }}</h3>
          </div>
          <Badge :variant="statusVariant(report.status)">{{ report.status }}</Badge>
        </div>
        
        <div class="text-sm text-[color:var(--color-text-muted)] mb-2">
          <strong>{{ t('admin.reason') }}:</strong> {{ t(`moderation.reason_${report.reason}`) }}
        </div>
        
        <div v-if="report.description" class="text-sm mb-2">
          {{ report.description }}
        </div>
        
        <div class="text-xs text-[color:var(--color-text-muted)] mb-3">
          {{ t('admin.reported_by') }}: {{ report.reporter.name }} ({{ report.reporter.email }})
        </div>
        
        <div v-if="report.status === 'pending'" class="flex gap-2">
          <button
            @click="handleResolve(report.id, 'remove')"
            class="btn-base h-9 px-3 rounded bg-red-500 text-white hover:bg-red-600"
          >
            {{ t('admin.remove_content') }}
          </button>
          <button
            @click="handleResolve(report.id, 'flag')"
            class="btn-base h-9 px-3 rounded border border-black/10 dark:border-white/10"
          >
            {{ t('admin.flag_content') }}
          </button>
          <button
            @click="handleDismiss(report.id)"
            class="btn-base h-9 px-3 rounded border border-black/10 dark:border-white/10"
          >
            {{ t('admin.dismiss') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import Badge from '@/components/ui/Badge.vue'
import Loader from '@/components/ui/Loader.vue'
import { fetchModerationReports, resolveReport, dismissReport } from '@/lib/api'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/admin/header.png'

const loading = ref(false)
const reports = ref<any[]>([])
const statusFilter = ref('pending')

onMounted(() => loadReports())

async function loadReports() {
  loading.value = true
  try {
    const res = await fetchModerationReports(statusFilter.value)
    reports.value = res.data
  } finally {
    loading.value = false
  }
}

async function handleResolve(id: number, action: 'remove' | 'flag' | 'warn') {
  const note = prompt(t('admin.resolution_note') as string)
  if (note === null) return
  
  try {
    await resolveReport(id, action, note)
    alert(t('admin.report_resolved'))
    loadReports()
  } catch (error) {
    alert(t('admin.action_error'))
  }
}

async function handleDismiss(id: number) {
  const note = prompt(t('admin.dismissal_note') as string)
  if (note === null) return
  
  try {
    await dismissReport(id, note)
    alert(t('admin.report_dismissed'))
    loadReports()
  } catch (error) {
    alert(t('admin.action_error'))
  }
}

function statusVariant(status: string) {
  const variants: Record<string, string> = {
    pending: 'warning',
    reviewing: 'info',
    resolved: 'success',
    dismissed: 'default',
  }
  return variants[status] || 'default'
}
</script>
