<template>
  <button
    @click="showModal = true"
    class="text-sm text-red-500 hover:text-red-700 underline"
  >
    {{ t('moderation.report') }}
  </button>

  <Teleport to="body">
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 z-50 grid place-items-center p-4"
      @click.self="closeModal"
    >
      <div class="bg-white dark:bg-gray-900 rounded-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-bold mb-4">{{ t('moderation.report_content') }}</h2>
        
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">{{ t('moderation.reason') }}</label>
            <select v-model="form.reason" required class="w-full rounded border border-black/10 dark:border-white/10 px-3 py-2 bg-white dark:bg-gray-800">
              <option value="spam">{{ t('moderation.reason_spam') }}</option>
              <option value="inappropriate">{{ t('moderation.reason_inappropriate') }}</option>
              <option value="offensive">{{ t('moderation.reason_offensive') }}</option>
              <option value="fake">{{ t('moderation.reason_fake') }}</option>
              <option value="other">{{ t('moderation.reason_other') }}</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">{{ t('moderation.description') }}</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full rounded border border-black/10 dark:border-white/10 px-3 py-2 bg-white dark:bg-gray-800"
              maxlength="500"
            />
          </div>
          
          <div class="flex gap-3 justify-end">
            <button
              type="button"
              @click="closeModal"
              class="btn-base h-10 px-4 rounded border border-black/10 dark:border-white/10"
            >
              {{ t('common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="btn-base h-10 px-4 rounded bg-red-500 text-white hover:bg-red-600"
            >
              {{ submitting ? t('common.loading') : t('moderation.submit_report') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { reportContent } from '@/lib/api'

const props = defineProps<{
  reportableType: string
  reportableId: number
}>()

const { t } = useI18n()
const showModal = ref(false)
const submitting = ref(false)

const form = reactive({
  reason: 'spam',
  description: ''
})

function closeModal() {
  showModal.value = false
}

async function handleSubmit() {
  submitting.value = true
  try {
    await reportContent({
      reportable_type: props.reportableType,
      reportable_id: props.reportableId,
      reason: form.reason,
      description: form.description || undefined
    })
    
    alert(t('moderation.report_success'))
    closeModal()
  } catch (error) {
    alert(t('moderation.report_error'))
  } finally {
    submitting.value = false
  }
}
</script>
