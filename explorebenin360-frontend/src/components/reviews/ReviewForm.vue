<template>
  <form @submit.prevent="onSubmit" class="space-y-3">
    <div class="flex items-center gap-2">
      <button v-for="i in 5" :key="i" type="button" @click="rating=i" class="text-2xl" :class="i<=rating ? 'text-yellow-500' : 'text-black/30 dark:text-white/30'">★</button>
    </div>
    <input v-model="title" type="text" placeholder="Titre (optionnel)" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 focus-ring" />
    <textarea v-model="body" rows="4" placeholder="Partagez votre expérience (min 10 caractères)" class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 focus-ring"></textarea>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="verified" :disabled="!!bookingId" /><span>J'ai utilisé ce service</span></label>
    <div class="flex gap-2">
      <button type="submit" class="btn-base focus-ring h-10 px-5 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }" :disabled="submitting || !valid">Envoyer</button>
      <div v-if="error" class="text-sm text-red-600">{{ error }}</div>
    </div>
  </form>
</template>
<script setup lang="ts">
import { ref, computed, watchEffect } from 'vue'
import { createReview } from '@/lib/services/reviews'

const props = defineProps<{ reviewableType: string; reviewableId: number; bookingId?: number }>()
const emit = defineEmits<{ (e: 'success'): void }>()

const rating = ref(0)
const title = ref('')
const body = ref('')
const verified = ref(!!props.bookingId)
const submitting = ref(false)
const error = ref('')

const valid = computed(() => rating.value >= 1 && (body.value?.trim().length || 0) >= 10)

watchEffect(() => { if (props.bookingId) verified.value = true })

async function onSubmit() {
  error.value = ''
  if (!valid.value) { error.value = 'Veuillez compléter les champs requis.'; return }
  submitting.value = true
  try {
    await createReview({ reviewable_type: props.reviewableType, reviewable_id: props.reviewableId, booking_id: props.bookingId, rating: rating.value, title: title.value || undefined, body: body.value })
    rating.value = 0; title.value = ''; body.value = ''
    emit('success')
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Erreur lors de l\'envoi.'
  } finally { submitting.value = false }
}
</script>
