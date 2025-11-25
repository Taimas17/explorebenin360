<script setup lang="ts">
import { ref, reactive } from 'vue'
import { createReview } from '@/lib/services/reviews'
import StarRating from './StarRating.vue'
import Loader from '@/components/ui/Loader.vue'

const props = defineProps<{
  isOpen: boolean
  bookingId: number
  offeringTitle: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'submitted'): void
}>()

const form = reactive({
  rating: 0,
  comment: '',
})

const loading = ref(false)
const error = ref<string | null>(null)

async function submit() {
  error.value = null
  if (form.rating === 0) {
    error.value = 'Veuillez sélectionner une note'
    return
  }
  loading.value = true
  try {
    await createReview({
      booking_id: props.bookingId,
      rating: form.rating,
      comment: form.comment || undefined,
    })
    emit('submitted')
    emit('close')
    form.rating = 0
    form.comment = ''
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Erreur lors de la soumission'
  } finally {
    loading.value = false
  }
}

function close() {
  if (!loading.value) {
    emit('close')
  }
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50" @click="close"></div>
    <div class="relative bg-[color:var(--bg)] rounded-lg shadow-lg w-full max-w-md p-6">
      <h3 class="text-xl font-semibold mb-4">Laisser un avis</h3>
      <p class="text-sm text-[color:var(--color-text-muted)] mb-4">{{ offeringTitle }}</p>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-2">Note *</label>
          <StarRating :rating="form.rating" @update:rating="form.rating = $event" size="lg" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">Commentaire (optionnel)</label>
          <textarea v-model="form.comment" rows="4" maxlength="1000"
                    class="w-full rounded-md border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 focus-ring text-sm"
                    placeholder="Partagez votre expérience..."></textarea>
          <div class="text-xs text-[color:var(--color-text-muted)] mt-1">{{ form.comment.length }} / 1000</div>
        </div>
        <div v-if="error" class="text-sm text-red-600 dark:text-red-400">{{ error }}</div>
        <div class="flex justify-end gap-2">
          <button type="button" @click="close" :disabled="loading"
                  class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border-white/10">
            Annuler
          </button>
          <button type="submit" :disabled="loading"
                  class="btn-base h-9 px-4 rounded-md text-white"
                  :style="{ backgroundColor: 'var(--color-primary)' }">
            <Loader v-if="loading" />
            <span v-else>Soumettre</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
