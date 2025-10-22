<template>
  <component :is="iconComp" class="h-6 w-6" />
</template>

<script setup>
import { computed } from 'vue'
import { HomeIcon, MapIcon, BookOpenIcon, BuildingOfficeIcon, UserIcon, SunIcon, MoonIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({ name: { type: String, required: true } })

const map = {
  Home: HomeIcon,
  Map: MapIcon,
  BookOpen: BookOpenIcon,
  Building: BuildingOfficeIcon,
  User: UserIcon,
  Sun: SunIcon,
  Moon: MoonIcon,
  ArrowRight: ArrowRightIcon,
}

const iconComp = computed(() => map[props.name] || ArrowRightIcon)
</script>
