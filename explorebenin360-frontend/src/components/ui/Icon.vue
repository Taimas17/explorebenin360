<template>
  <component :is="iconComp" class="h-6 w-6" />
</template>

<script setup>
import { computed } from 'vue'
import { HomeIcon, MapIcon, BookOpenIcon, BuildingOfficeIcon, UserIcon, SunIcon, MoonIcon, ArrowRightIcon, CheckIcon, WifiIcon, SparklesIcon, TruckIcon, TvIcon, HomeModernIcon, Cog6ToothIcon, HeartIcon } from '@heroicons/vue/24/outline'

const props = defineProps({ name: { type: String, required: true } })

const map: Record<string, any> = {
  Home: HomeIcon,
  Map: MapIcon,
  BookOpen: BookOpenIcon,
  Building: BuildingOfficeIcon,
  User: UserIcon,
  Sun: SunIcon,
  Moon: MoonIcon,
  ArrowRight: ArrowRightIcon,
  Check: CheckIcon,
  Wifi: WifiIcon,
  Sparkles: SparklesIcon,
  Truck: TruckIcon,
  Tv: TvIcon,
  HomeModern: HomeModernIcon,
  Cog: Cog6ToothIcon,
  Heart: HeartIcon,
}

const iconComp = computed(() => map[props.name] || ArrowRightIcon)
</script>
