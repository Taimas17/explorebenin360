<template>
  <div class="border border-black/10 dark:border-white/10 rounded-md p-4">
    <div class="flex items-center gap-4">
      <div class="text-4xl font-bold">{{ summary.average.toFixed(1) }}</div>
      <div class="flex items-center gap-1 text-yellow-500">
        <span v-for="i in 5" :key="i">{{ i <= Math.round(summary.average) ? '★' : '☆' }}</span>
      </div>
      <div class="text-sm text-[color:var(--color-text-muted)]">{{ summary.total }} avis</div>
    </div>
    <div class="mt-4 space-y-1">
      <div v-for="i in [5,4,3,2,1]" :key="i" class="flex items-center gap-2">
        <div class="w-10 text-sm">{{ i }} ★</div>
        <div class="flex-1 h-2 rounded bg-black/10 dark:bg-white/10 overflow-hidden">
          <div class="h-full bg-[color:var(--color-primary)]" :style="{ width: barWidth(i) }" />
        </div>
        <div class="w-10 text-right text-sm text-[color:var(--color-text-muted)]">{{ summary[(`${i}_star` as any)] }}</div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import type { ReviewsSummary } from '@/types/business'
const props = defineProps<{ summary: ReviewsSummary }>()

function barWidth(star: number) {
  const total = props.summary.total || 1
  const count = (props.summary as any)[`${star}_star`] || 0
  const pct = Math.min(100, Math.round((count / total) * 100))
  return `${pct}%`
}
</script>
