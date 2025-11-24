<template>
  <div class="relative inline-block" :style="{ width: size+'px' }">
    <div class="relative">
      <img v-if="previewUrl || currentAvatarUrl" :src="previewUrl || currentAvatarUrl!" :alt="'Avatar'" :class="['rounded-full object-cover', sizeClass]" />
      <div v-else :class="['rounded-full bg-gradient-to-br from-[color:var(--color-secondary)] to-[color:var(--color-accent)] flex items-center justify-center text-white font-semibold', sizeClass]">
        {{ initials }}
      </div>
      <input ref="fileInput" type="file" class="hidden" accept="image/*" @change="onFileChange" />
      <button @click="pickFile" class="absolute bottom-0 right-0 btn-base h-8 px-3 rounded-full text-xs bg-[color:var(--color-secondary)] text-white focus-ring" :disabled="loading">
        <span v-if="!loading">{{ previewUrl ? t('common.save') : t('common.change') }}</span>
        <span v-else>{{ t('common.uploading') }}...</span>
      </button>
      <button v-if="previewUrl" @click="cancelPreview" class="absolute bottom-0 left-0 btn-base h-8 px-3 rounded-full text-xs border border-black/10 dark:border-white/10 bg-[color:var(--bg)]/80 backdrop-blur focus-ring">{{ t('common.cancel') }}</button>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { uploadAvatar } from '@/lib/services/profile'

const props = withDefaults(defineProps<{ currentAvatarUrl?: string | null; name?: string; size?: number }>(), { size: 96 })
const emit = defineEmits<{ (e: 'uploaded', url: string): void }>()
const { t } = useI18n()

const fileInput = ref<HTMLInputElement | null>(null)
const loading = ref(false)
const previewUrl = ref<string | null>(null)

const sizeClass = computed(() => props.size! >= 96 ? 'w-24 h-24 sm:w-28 sm:h-28' : 'w-16 h-16')
const initials = computed(() => (props.name || 'EB').split(' ').map(s => s[0]).join('').slice(0,2).toUpperCase())

function pickFile() {
  if (previewUrl.value) return save()
  fileInput.value?.click()
}

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return
  previewUrl.value = URL.createObjectURL(file)
}

async function save() {
  const file = fileInput.value?.files?.[0]
  if (!file) return
  loading.value = true
  try {
    const url = await uploadAvatar(file)
    emit('uploaded', url)
    previewUrl.value = null
    if (fileInput.value) fileInput.value.value = ''
  } finally {
    loading.value = false
  }
}

function cancelPreview() {
  previewUrl.value = null
  if (fileInput.value) fileInput.value.value = ''
}
</script>
