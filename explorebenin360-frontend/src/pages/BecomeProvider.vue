<template>
  <div class="container-px mx-auto py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">{{ t('provider.become_provider') }}</h1>
    
    <div v-if="providerStatus && providerStatus !== 'none'" class="mb-6">
      <Alert :variant="alertVariant">
        <template v-if="providerStatus === 'pending'">
          {{ t('provider.status_pending') }}
        </template>
        <template v-else-if="providerStatus === 'approved'">
          {{ t('provider.status_approved') }}
          <RouterLink to="/provider" class="underline ml-2">
            {{ t('provider.go_to_dashboard') }}
          </RouterLink>
        </template>
        <template v-else-if="providerStatus === 'rejected'">
          {{ t('provider.status_rejected') }}: {{ rejectionReason }}
        </template>
      </Alert>
    </div>
    
    <form v-if="!providerStatus || providerStatus === 'none' || providerStatus === 'rejected'" @submit.prevent="handleSubmit" class="space-y-6">
      <div>
        <label class="block text-sm font-medium mb-2">
          {{ t('provider.business_name') }} *
        </label>
        <input
          v-model="form.business_name"
          type="text"
          required
          class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring"
        />
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">
          {{ t('provider.phone') }} *
        </label>
        <input
          v-model="form.phone"
          type="tel"
          required
          class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring"
        />
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">
          {{ t('provider.bio') }} *
        </label>
        <textarea
          v-model="form.bio"
          rows="5"
          required
          minlength="50"
          class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring"
        />
        <div class="text-xs text-[color:var(--color-text-muted)] mt-1">
          {{ form.bio.length }} / 1000
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">
          {{ t('provider.kyc_documents') }} ({{ t('common.optional') }})
        </label>
        <input
          type="url"
          v-model="docUrl"
          @keydown.enter.prevent="addDocument"
          placeholder="https://..."
          class="w-full rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring"
        />
        <button
          type="button"
          @click="addDocument"
          class="mt-2 btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10 text-sm"
        >
          {{ t('provider.add_document') }}
        </button>
        
        <ul v-if="form.kyc_documents.length > 0" class="mt-3 space-y-1">
          <li
            v-for="(doc, idx) in form.kyc_documents"
            :key="idx"
            class="flex items-center justify-between text-sm"
          >
            <a :href="doc" target="_blank" class="underline truncate">{{ doc }}</a>
            <button
              type="button"
              @click="removeDocument(idx)"
              class="text-red-500 hover:text-red-700 ml-2"
            >
              ×
            </button>
          </li>
        </ul>
      </div>
      
      <button
        type="submit"
        :disabled="submitting"
        class="w-full btn-base focus-ring h-12 rounded-md text-white font-medium"
        :style="{ backgroundColor: 'var(--color-primary)' }"
      >
        <Loader v-if="submitting" />
        <span v-else>{{ t('provider.submit_application') }}</span>
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import Alert from '@/components/ui/Alert.vue'
import Loader from '@/components/ui/Loader.vue'
import { applyAsProvider, fetchProviderStatus } from '@/lib/api'

const { t } = useI18n()
const router = useRouter()

const form = reactive({
  business_name: '',
  phone: '',
  bio: '',
  kyc_documents: [] as string[]
})

const docUrl = ref('')
const submitting = ref(false)
const providerStatus = ref<string | null>(null)
const rejectionReason = ref<string | null>(null)

const alertVariant = computed(() => {
  switch (providerStatus.value) {
    case 'pending': return 'warning'
    case 'approved': return 'success'
    case 'rejected': return 'error'
    default: return 'default'
  }
})

onMounted(async () => {
  try {
    const res: any = await fetchProviderStatus()
    providerStatus.value = res.data.provider_status
    rejectionReason.value = res.data.rejection_reason
  } catch (error) {
    // User n'est pas provider
  }
})

function addDocument() {
  const url = docUrl.value.trim()
  if (url && url.startsWith('http')) {
    form.kyc_documents.push(url)
    docUrl.value = ''
  }
}

function removeDocument(index: number) {
  form.kyc_documents.splice(index, 1)
}

async function handleSubmit() {
  submitting.value = true
  try {
    await applyAsProvider({
      business_name: form.business_name,
      phone: form.phone,
      bio: form.bio,
      kyc_documents: form.kyc_documents.length > 0 ? form.kyc_documents : undefined
    })
    
    alert(t('provider.application_success'))
    providerStatus.value = 'pending'
  } catch (error: any) {
    alert(error.response?.data?.message || t('provider.application_error'))
  } finally {
    submitting.value = false
  }
}
</script>
