<template>
  <button
    :aria-label="ariaLabel"
    role="switch"
    :aria-checked="checked ? 'true' : 'false'"
    :class="btnClass"
    @click.stop="onToggle"
    @keydown.enter.prevent.stop="onToggle"
    @keydown.space.prevent.stop="onToggle"
  >
    <component :is="checked ? HeartSolid : HeartOutline" :class="iconClass" />
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useFavoritesStore, type FavType } from '@/stores/favorites'
import { HeartIcon as HeartOutline } from '@heroicons/vue/24/outline'
import { HeartIcon as HeartSolid } from '@heroicons/vue/24/solid'

const props = withDefaults(defineProps<{ type: FavType; id: number; size?: 'sm'|'md'; ariaLabel?: string; entity?: any }>(), { size: 'md' })
const emit = defineEmits<{ (e: 'change', value: boolean): void }>()
const { t } = useI18n()
const fav = useFavoritesStore()

const checked = computed(() => fav.isFav(props.type, props.id))
const ariaLabel = computed(() => props.ariaLabel || (checked.value ? t('favorites.remove_aria') : t('favorites.add_aria')))

const btnClass = computed(() => [
  'focus-ring rounded-full inline-flex items-center justify-center transition-colors',
  props.size === 'sm' ? 'h-8 w-8' : 'h-10 w-10',
  'backdrop-blur border',
  checked.value
    ? 'bg-red-500 text-white border-transparent'
    : 'bg-white/80 dark:bg-white/10 text-[color:var(--color-text)]/90 border-black/10 dark:border-white/10 hover:bg-white hover:dark:bg-white/20'
])

const iconClass = computed(() => props.size === 'sm' ? 'h-4 w-4' : 'h-5 w-5')

function onToggle() {
  fav.toggle(props.type, props.id, props.entity)
  emit('change', !checked.value)
}
</script>
