<template>
  <div>
    <div v-if="previewUrl" class="mb-2">
      <img :src="previewUrl" alt="Preview" class="w-48 h-32 object-cover rounded-md border border-black/10 dark:border-white/10" />
    </div>
    <input type="file" accept="image/*" @change="onFile" />
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import api from '@/lib/api'

const emit = defineEmits<{ (e: 'uploaded', url: string): void }>()
const previewUrl = ref<string | null>(null)

async function onFile(e: Event) {
  const input = e.target as HTMLInputElement
  if (!input.files || !input.files[0]) return
  const file = input.files[0]
  previewUrl.value = URL.createObjectURL(file)
  const fd = new FormData()
  fd.append('file', file)
  try {
    const { data } = await api.post('/media', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    const uploaded = Array.isArray(data) ? data[0] : data
    if (uploaded?.url) emit('uploaded', uploaded.url)
  } catch (err) {
    console.error('Upload failed', err)
  }
}
</script>
