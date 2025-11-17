<template>
  <div class="inline-flex items-center gap-1" :class="containerClass">
    <button
      v-for="star in 5"
      :key="star"
      type="button"
      :disabled="readonly"
      @click="!readonly && handleClick(star)"
      @mouseenter="!readonly && handleHover(star)"
      @mouseleave="!readonly && handleHover(0)"
      :class="[
        'focus-ring rounded',
        !readonly && 'cursor-pointer hover:scale-110 transition-transform'
      ]"
      :aria-label="`${star} étoile${star > 1 ? 's' : ''}`"
    >
      <component
        :is="star <= (hoverRating || modelValue) ? StarSolid : StarOutline"
        :class="[
          iconClass,
          star <= (hoverRating || modelValue) ? 'text-yellow-500' : 'text-gray-300 dark:text-gray-600'
        ]"
      />
    </button>
    
    <span v-if="showValue" class="text-sm font-medium ml-1">
      {{ modelValue.toFixed(1) }}
    </span>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { StarIcon as StarOutline } from '@heroicons/vue/24/outline'
import { StarIcon as StarSolid } from '@heroicons/vue/24/solid'

const props = withDefaults(
  defineProps<{
    modelValue?: number
    rating?: number
    readonly?: boolean
    size?: 'sm' | 'md' | 'lg'
    showValue?: boolean
  }>(),
  { modelValue: 0, readonly: false, size: 'md', showValue: false }
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: number): void
}>()

const hoverRating = ref(0)

const containerClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'gap-0.5'
    case 'lg': return 'gap-2'
    default: return 'gap-1'
  }
})

const iconClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'h-4 w-4'
    case 'lg': return 'h-8 w-8'
    default: return 'h-5 w-5'
  }
})

const modelValue = computed({
  get: () => props.rating ?? props.modelValue,
  set: (value: number) => emit('update:modelValue', value)
})

function handleClick(rating: number) {
  modelValue.value = rating
}

function handleHover(rating: number) {
  hoverRating.value = rating
}
</script>
