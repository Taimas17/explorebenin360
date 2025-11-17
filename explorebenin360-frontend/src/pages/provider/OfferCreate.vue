<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import Loader from '@/components/ui/Loader.vue'
import { createOffering } from '@/lib/services/offerings'

const { t } = useI18n()
const router = useRouter()

const form = reactive({
  title: '',
  type: 'accommodation',
  description: '',
  price: 0,
  currency: 'XOF',
  capacity: 1,
  place_id: null as number | null,
  cover_image_url: '',
  gallery: [] as string[],
  cancellation_policy: '',
  status: 'draft'
})

const submitting = ref(false)

async function handleSubmit() {
  submitting.value = true
  try {
    const created: any = await createOffering(form)
    alert(t('provider.offer_created'))
    router.push(`/provider/offers/${created.id}`)
  } catch (error: any) {
    alert(error.response?.data?.message || t('provider.offer_create_error'))
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <div>
      <label class="block text-sm font-medium mb-2">{{ t('provider.title') }} *</label>
      <input v-model="form.title" required class="w-full rounded-md border px-3 py-2 focus-ring" />
    </div>
    
    <div>
      <label class="block text-sm font-medium mb-2">{{ t('provider.type') }} *</label>
      <select v-model="form.type" required class="w-full rounded-md border px-3 py-2 focus-ring">
        <option value="accommodation">{{ t('provider.type_accommodation') }}</option>
        <option value="experience">{{ t('provider.type_experience') }}</option>
        <option value="guide_service">{{ t('provider.type_guide_service') }}</option>
      </select>
    </div>
    
    <div>
      <label class="block text-sm font-medium mb-2">{{ t('provider.description') }}</label>
      <textarea v-model="form.description" rows="5" class="w-full rounded-md border px-3 py-2 focus-ring" />
    </div>
    
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-2">{{ t('provider.price') }} *</label>
        <input v-model.number="form.price" type="number" min="0" required class="w-full rounded-md border px-3 py-2 focus-ring" />
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">{{ t('provider.capacity') }}</label>
        <input v-model.number="form.capacity" type="number" min="1" class="w-full rounded-md border px-3 py-2 focus-ring" />
      </div>
    </div>
    
    <div>
      <label class="block text-sm font-medium mb-2">{{ t('provider.cover_image') }}</label>
      <input v-model="form.cover_image_url" type="url" placeholder="https://..." class="w-full rounded-md border px-3 py-2 focus-ring" />
    </div>
    
    <div>
      <label class="block text-sm font-medium mb-2">{{ t('provider.cancellation_policy') }}</label>
      <textarea v-model="form.cancellation_policy" rows="3" class="w-full rounded-md border px-3 py-2 focus-ring" />
    </div>
    
    <div class="flex gap-3">
      <button
        type="submit"
        :disabled="submitting"
        class="btn-base focus-ring h-10 px-6 rounded-md text-white"
        :style="{ backgroundColor: 'var(--color-primary)' }"
      >
        <Loader v-if="submitting" />
        <span v-else>{{ form.status === 'published' ? t('provider.create_and_publish') : t('provider.save_draft') }}</span>
      </button>
      
      <button
        type="button"
        @click="form.status = form.status === 'draft' ? 'published' : 'draft'"
        class="btn-base focus-ring h-10 px-4 rounded-md border"
      >
        {{ form.status === 'published' ? t('provider.save_as_draft') : t('provider.publish_now') }}
      </button>
    </div>
  </form>
</template>
