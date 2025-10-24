<template>
  <span :class="cls">{{ label }}</span>
</template>
<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps<{ status: 'pending'|'authorized'|'confirmed'|'cancelled'|'refunded' }>()
const { t } = useI18n()

const COLORS: Record<string, string> = {
  pending: 'bg-amber-500/15 text-amber-700 dark:text-amber-300',
  authorized: 'bg-blue-500/15 text-blue-700 dark:text-blue-300',
  confirmed: 'bg-green-500/15 text-green-700 dark:text-green-300',
  cancelled: 'bg-red-500/15 text-red-700 dark:text-red-400',
  refunded: 'bg-purple-500/15 text-purple-700 dark:text-purple-300',
}

const cls = computed(() => `inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ${COLORS[props.status] || 'bg-gray-500/15 text-gray-600 dark:text-gray-300'}`)
const label = computed(() => t(`statuses.${props.status}`))
</script>
