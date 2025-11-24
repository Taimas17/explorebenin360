<template>
  <div class="relative w-full h-40 sm:h-56 md:h-64 rounded-lg overflow-hidden bg-gradient-to-br from-[color:var(--color-secondary)]/20 to-[color:var(--color-accent)]/20">
    <img v-if="previewUrl || currentCoverUrl" :src="previewUrl || currentCoverUrl!" alt="Cover" class="w-full h-full object-cover" />
    <div class="absolute inset-0 bg-black/0 hover:bg-black/20 transition-colors"></div>
    <div class="absolute bottom-2 right-2 flex gap-2">
      <input ref="fileInput" type="file" class="hidden" accept="image/*" @change="onFileChange" />
      <button @click="pickFile" class="btn-base h-9 px-4 rounded-md bg-[color:var(--color-secondary)] text-white focus-ring" :disabled="loading">
        <span v-if="!loading">{{ previewUrl ? t('common.save') : t('profile.change_cover') }}</span>
        <span v-else>{{ t('common.uploading') }}...</span>
      </button>
      <button v-if="previewUrl" @click="cancelPreview" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10 bg-[color:var(--bg)]/80 backdrop-blur focus-ring">{{ t('common.cancel') }}</button>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { uploadCover } from '@/lib/services/profile'

const props = defineProps<{ currentCoverUrl?: string | null }>()
const emit = defineEmits<{ (e: 'uploaded', url: string): void }>()
const { t } = useI18n()

const fileInput = ref<HTMLInputElement | null>(null)
const previewUrl = ref<string | null>(null)
const loading = ref(false)

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
    const url = await uploadCover(file)
    emit('uploaded', url)
    previewUrl.value = null
    if (fileInput.value) fileInput.value.value = ''
  } finally { loading.value = false }
}

function cancelPreview() {
  previewUrl.value = null
  if (fileInput.value) fileInput.value.value = ''
}
</script>
