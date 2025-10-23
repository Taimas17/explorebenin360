<template>
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
    <div v-for="a in normalized" :key="a.key" class="flex items-center gap-2 p-2 rounded-md ring-1 ring-black/5 dark:ring-white/10 bg-white/50 dark:bg-white/5">
      <Icon :name="a.icon" />
      <span class="text-sm" :aria-label="a.label">{{ a.label }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Icon from '@/components/ui/Icon.vue'

type AmenitiesInput = string[] | Record<string, boolean>

const props = defineProps<{ amenities: AmenitiesInput }>()

const LABELS: Record<string, { label: string; icon: string }> = {
  Wifi: { label: 'Wi‑Fi', icon: 'Wifi' },
  AirConditioning: { label: 'Climatisation', icon: 'Sparkles' },
  Parking: { label: 'Parking', icon: 'Truck' },
  BreakfastIncluded: { label: 'Petit‑déjeuner', icon: 'Sparkles' },
  Pool: { label: 'Piscine', icon: 'Sun' },
  Kitchen: { label: 'Cuisine', icon: 'HomeModern' },
  TV: { label: 'TV', icon: 'Tv' },
  Washer: { label: 'Lave‑linge', icon: 'Cog' },
  Dryer: { label: 'Sèche‑linge', icon: 'Cog' },
  PetsAllowed: { label: 'Animaux acceptés', icon: 'Heart' },
}

const SYNONYMS: Record<string, keyof typeof LABELS> = {
  wifi: 'Wifi',
  wi_fi: 'Wifi',
  internet: 'Wifi',
  airconditioning: 'AirConditioning',
  air_conditioning: 'AirConditioning',
  ac: 'AirConditioning',
  parking: 'Parking',
  breakfast: 'BreakfastIncluded',
  breakfastincluded: 'BreakfastIncluded',
  petitdejeuner: 'BreakfastIncluded',
  pool: 'Pool',
  piscine: 'Pool',
  kitchen: 'Kitchen',
  cuisine: 'Kitchen',
  tv: 'TV',
  television: 'TV',
  washer: 'Washer',
  laundry: 'Washer',
  dryer: 'Dryer',
  pets: 'PetsAllowed',
  petsallowed: 'PetsAllowed',
  animaux: 'PetsAllowed',
}

const normalized = computed(() => {
  const keys = new Set<keyof typeof LABELS>()
  if (Array.isArray(props.amenities)) {
    props.amenities.forEach((raw) => {
      const k = (raw || '').toString().toLowerCase().replace(/\s|-/g, '')
      const mapped = SYNONYMS[k as keyof typeof SYNONYMS]
      if (mapped) keys.add(mapped)
    })
  } else if (props.amenities && typeof props.amenities === 'object') {
    Object.entries(props.amenities).forEach(([raw, val]) => {
      if (!val) return
      const k = raw.toLowerCase().replace(/\s|-/g, '')
      const mapped = SYNONYMS[k as keyof typeof SYNONYMS] as keyof typeof LABELS
      if (mapped) keys.add(mapped)
    })
  }
  return Array.from(keys).map((k) => ({ key: k, ...LABELS[k] }))
})
</script>
