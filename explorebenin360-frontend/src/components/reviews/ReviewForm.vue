<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-2">
        {{ t('reviews.your_rating') }} *
      </label>
      <StarRating v-model="form.rating" size="lg" />
      <span v-if="errors.rating" class="text-sm text-red-500 mt-1">{{ errors.rating }}</span>
    </div>
    
    <div>
      <label class="block text-sm font-medium mb-2">
        {{ t('reviews.your_comment') }} *
      </label>
      <textarea
        v-model="form.comment"
        rows="5"
        :placeholder="t('reviews.comment_placeholder')"
        class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring"
        maxlength="2000"
      />
      <div class="text-xs text-[color:var(--color-text-muted)] mt-1">
        {{ form.comment.length }}/2000
      </div>
      <span v-if="errors.comment" class="text-sm text-red-500 mt-1">{{ errors.comment }}</span>
    </div>
    
    <div>
      <label class="block text-sm font-medium mb-2">
        {{ t('reviews.photos_optional') }}
      </label>
      <input
        type="url"
        v-model="photoUrl"
        @keydown.enter.prevent="addPhoto"
        :placeholder="t('reviews.photo_url_placeholder')"
        class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring"
      />
      <button
        type="button"
        @click="addPhoto"
        class="mt-2 btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10 text-sm"
      >
        {{ t('reviews.add_photo') }}
      </button>
      <span v-if="errors.photos" class="block text-sm text-red-500 mt-1">{{ errors.photos }}</span>
      
      <div v-if="form.photos.length > 0" class="mt-3 grid grid-cols-5 gap-2">
        <div
          v-for="(photo, idx) in form.photos"
          :key="idx"
          class="relative aspect-square rounded-md overflow-hidden border border-black/10 dark:border-white/10"
        >
          <EBImage :src="photo" :alt="`Photo ${idx + 1}`" :width="200" :height="200" aspect-ratio="1/1" />
          <button
            type="button"
            @click="removePhoto(idx)"
            class="absolute top-1 right-1 bg-red-500 text-white rounded-full h-6 w-6 flex items-center justify-center hover:bg-red-600 focus-ring"
            :aria-label="t('reviews.remove_photo')"
          >
            ×
          </button>
        </div>
      </div>
    </div>
    
    <div class="flex justify-end gap-3">
      <button
        type="button"
        @click="$emit('cancel')"
        class="btn-base focus-ring h-10 px-4 rounded-md border border-black/10 dark:border-white/10"
      >
        {{ t('common.cancel') }}
      </button>
      <button
        type="submit"
        :disabled="submitting"
        class="btn-base focus-ring h-10 px-4 rounded-md text-white"
        :style="{ backgroundColor: 'var(--color-primary)' }"
      >
        <Loader v-if="submitting" />
        <span v-else>{{ t('reviews.submit') }}</span>
      </button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import StarRating from '@/components/ui/StarRating.vue'
import EBImage from '@/components/media/EBImage.vue'
import Loader from '@/components/ui/Loader.vue'
import { submitReview } from '@/lib/api'

const props = defineProps<{
  bookingId: number
}>()

const emit = defineEmits<{
  (e: 'success'): void
  (e: 'cancel'): void
}>()

const { t } = useI18n()

const form = reactive({
  rating: 0,
  comment: '',
  photos: [] as string[]
})

const photoUrl = ref('')
const errors = reactive<Record<string, string>>({})
const submitting = ref(false)

function addPhoto() {
  const url = photoUrl.value.trim()
  if (!url) return
  
  if (!url.startsWith('http')) {
    errors.photos = 'URL invalide'
    return
  }
  
  if (form.photos.length >= 5) {
    errors.photos = 'Maximum 5 photos'
    return
  }
  
  form.photos.push(url)
  photoUrl.value = ''
  delete errors.photos
}

function removePhoto(index: number) {
  form.photos.splice(index, 1)
}

async function handleSubmit() {
  errors.rating = ''
  errors.comment = ''
  
  if (form.rating === 0) {
    errors.rating = t('reviews.rating_required')
    return
  }
  
  if (form.comment.length < 10) {
    errors.comment = t('reviews.comment_min_length')
    return
  }
  
  submitting.value = true
  try {
    await submitReview({
      booking_id: props.bookingId,
      rating: form.rating,
      comment: form.comment,
      photos: form.photos.length > 0 ? form.photos : undefined
    })
    
    emit('success')
  } catch (error: any) {
    if (error.response?.data?.message) {
      alert(error.response.data.message)
    } else {
      alert("Erreur lors de la soumission de l'avis")
    }
  } finally {
    submitting.value = false
  }
}
</script>
