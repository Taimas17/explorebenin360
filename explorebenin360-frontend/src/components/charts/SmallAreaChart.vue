<template>
  <div class="w-full h-32">
    <svg :viewBox="`0 0 ${width} ${height}`" class="w-full h-full">
      <defs>
        <linearGradient :id="gid" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" :stop-color="color" stop-opacity="0.35"/>
          <stop offset="100%" :stop-color="color" stop-opacity="0"/>
        </linearGradient>
      </defs>
      <path :d="areaPath" :fill="`url(#${gid})`" />
      <path :d="linePath" :stroke="color" fill="none" stroke-width="2" />
    </svg>
  </div>
</template>
<script setup lang="ts">
import { computed } from 'vue'
import type { TimePoint } from '@/lib/services/analytics'

const props = withDefaults(defineProps<{ data: TimePoint[]; color?: string }>(), { color: '#06b6d4' })
const width = 300, height = 100, pad = 6
const gid = `grad-${Math.random().toString(36).slice(2)}`

const x = (i: number, n: number) => pad + (i * (width - pad * 2)) / Math.max(1, n - 1)
const y = (v: number, min: number, max: number) => height - pad - ((v - min) * (height - pad * 2)) / Math.max(1, max - min)

const linePath = computed(() => {
  const n = props.data.length
  const vals = props.data.map(d => d.value)
  const min = Math.min(...vals)
  const max = Math.max(...vals)
  return props.data.map((d, i) => `${i === 0 ? 'M' : 'L'} ${x(i, n)} ${y(d.value, min, max)}`).join(' ')
})

const areaPath = computed(() => {
  const n = props.data.length
  const vals = props.data.map(d => d.value)
  const min = Math.min(...vals)
  const max = Math.max(...vals)
  const top = props.data.map((d, i) => `${i === 0 ? 'M' : 'L'} ${x(i, n)} ${y(d.value, min, max)}`).join(' ')
  const lastX = x(n - 1, n)
  return `${top} L ${lastX} ${height - pad} L ${pad} ${height - pad} Z`
})
</script>
