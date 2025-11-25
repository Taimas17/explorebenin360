<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  rating: number
  readonly?: boolean
  size?: 'sm' | 'md' | 'lg'
}>()

const emit = defineEmits<{
  (e: 'update:rating', value: number): void
}>()

const sizeClass = computed(() => {
  return {
    sm: 'w-4 h-4',
    md: 'w-5 h-5',
    lg: 'w-6 h-6',
  }[props.size || 'md']
})

function selectRating(value: number) {
  if (!props.readonly) {
    emit('update:rating', value)
  }
}
</script>

<template>
  <div class="flex items-center gap-1">
    <button
      v-for="star in 5"
      :key="star"
      type="button"
      @click="selectRating(star)"
      :disabled="readonly"
      :class="[
        sizeClass,
        readonly ? 'cursor-default' : 'cursor-pointer hover:scale-110 transition-transform',
        star <= rating ? 'text-yellow-500' : 'text-gray-300 dark:text-gray-600'
      ]"
    >
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
      </svg>
    </button>
  </div>
</template>
