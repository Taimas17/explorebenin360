<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Messages" :title="t('dashboard.messages')" class="mb-6" />

    <div class="flex items-center justify-between mb-3">
      <div class="text-xs text-[color:var(--color-text-muted)] inline-flex items-center gap-1">
        <Icon name="Mail" class="h-4 w-4"/> {{ totalUnread }}
      </div>
      <button @click="openNewMessage" class="btn-base focus-ring h-9 px-3 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">{{ t('dashboard.new_message') || 'Nouveau message' }}</button>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
      <div class="md:col-span-1 border border-black/10 dark:border-white/10 rounded-md overflow-hidden">
        <div class="px-3 py-2 text-xs text-[color:var(--color-text-muted)] flex items-center justify-between">
          <span>{{ t('dashboard.threads') }}</span>
        </div>
        <div v-if="loading" class="p-3 text-sm text-[color:var(--color-text-muted)]"><Loader /> {{ t('common.loading') }}</div>
        <Alert v-else-if="error" type="error" class="m-3">{{ error }}</Alert>
        <ul v-else class="divide-y divide-black/10 dark:divide-white/10">
          <li v-for="th in threads" :key="th.id">
            <button @click="select(th.id)" class="w-full text-left px-3 py-2 hover:bg-black/5 dark:hover:bg:white/5 focus-ring">
              <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                  <div class="font-medium text-sm truncate">{{ th.subject }}</div>
                  <div class="text-xs text-[color:var(--color-text-muted)] truncate">{{ th.last_message_preview }}</div>
                </div>
                <div class="text-right">
                  <div class="text-[10px] text-[color:var(--color-text-muted)]">{{ formatDate(th.last_message_at) }}</div>
                  <span v-if="th.unread_count" class="inline-flex items-center justify-center text-[10px] min-w-5 h-5 rounded-full bg-[color:var(--color-secondary)]/20 text-[color:var(--color-secondary)] px-1">{{ th.unread_count }}</span>
                </div>
              </div>
            </button>
          </li>
        </ul>
      </div>

      <div class="md:col-span-2 border border-black/10 dark:border:white/10 rounded-md p-3 flex flex-col">
        <div v-if="!activeId" class="grow grid place-items-center py-8">
          <EmptyState variant="default" :title="t('dashboard.pick_thread')" />
        </div>
        <div v-else class="grow flex flex-col">
          <div class="mb-3">
            <div class="font-semibold">{{ activeThread?.subject }}</div>
            <div v-if="threadDetail" class="text-xs text-[color:var(--color-text-muted)]">
              <template v-if="threadDetail.offering">{{ threadDetail.offering.title }}</template>
              <template v-else-if="threadDetail.booking">#{{ threadDetail.booking.id }} · {{ threadDetail.booking.offering_title }} · {{ threadDetail.booking.start_date }}</template>
            </div>
          </div>
          <div class="grow space-y-3 overflow-y-auto max-h-[50vh] pr-2">
            <div v-for="m in activeMessages" :key="m.id" class="rounded-md border border-black/10 dark:border:white/10 p-2" :class="{ 'opacity-70': !!m.read_at }">
              <div class="text-xs text-[color:var(--color-text-muted)] mb-1">{{ m.sender.name }} · {{ formatDate(m.created_at) }}</div>
              <div class="text-sm whitespace-pre-line">{{ m.body }}</div>
            </div>
          </div>
          <form class="mt-3 flex gap-2" @submit.prevent="onSend" aria-label="{{ t('dashboard.send_message') }}">
            <input v-model="draft" class="flex-1 rounded-md border border-black/10 dark:border:white/10 px-3 py-2 bg-transparent focus-ring" :placeholder="t('dashboard.type_message')" />
            <button :disabled="!activeId || !draft.trim() || sending" class="btn-base focus-ring h-10 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
              <span v-if="sending"><Loader /></span>
              <span v-else>{{ t('dashboard.send') }}</span>
            </button>
          </form>
        </div>
      </div>
    </div>

    <div v-if="modals.newMessage" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="modals.newMessage=false"></div>
      <div class="relative bg-[color:var(--bg)] rounded-lg shadow-lg w-full max-w-md p-5">
        <h3 class="text-lg font-semibold mb-3">{{ t('dashboard.new_message') || 'Nouveau message' }}</h3>
        <form @submit.prevent="createNewThread" class="space-y-3">
          <div>
            <label class="block text-sm mb-1">Provider ID</label>
            <input v-model.number="newForm.provider_id" type="number" required class="w-full rounded-md border border-black/10 dark:border:white/10 bg-transparent px-3 py-2 focus-ring" />
          </div>
          <div>
            <label class="block text-sm mb-1">{{ t('dashboard.subject') || 'Sujet' }}</label>
            <input v-model="newForm.subject" required maxlength="255" class="w-full rounded-md border border-black/10 dark:border:white/10 bg-transparent px-3 py-2 focus-ring" />
          </div>
          <div>
            <label class="block text-sm mb-1">{{ t('dashboard.message') || 'Message' }}</label>
            <textarea v-model="newForm.initial_message" required maxlength="5000" rows="4" class="w-full rounded-md border border-black/10 dark:border:white/10 bg-transparent px-3 py-2 focus-ring"></textarea>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" class="btn-base h-9 px-3 rounded-md border border-black/10 dark:border:white/10" @click="modals.newMessage=false">{{ t('common.cancel') }}</button>
            <button type="submit" class="btn-base h-9 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }" :disabled="creating">
              <span v-if="creating"><Loader /></span>
              <span v-else>{{ t('common.create') }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import Loader from '@/components/ui/Loader.vue'
import Icon from '@/components/ui/Icon.vue'
import Alert from '@/components/ui/Alert.vue'
import { listThreads, listMessages, sendMessage, createThread } from '@/lib/services/messages'
import api from '@/lib/api'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/traveler/header.png'

const threads = ref([])
const activeId = ref(null)
const activeThread = ref(null)
const threadDetail = ref(null)
const messages = ref({})
const draft = ref('')
const sending = ref(false)
const loading = ref(false)
const error = ref(null)
const creating = ref(false)
const modals = ref({ newMessage: false })
const newForm = ref({ subject: '', provider_id: null, initial_message: '' })

const totalUnread = computed(() => threads.value.reduce((s, th) => s + (th.unread_count || 0), 0))
const activeMessages = computed(() => (activeId.value ? (messages.value[activeId.value] || []) : []))

onMounted(load)

async function load() {
  loading.value = true
  error.value = null
  try {
    threads.value = await listThreads()
    if (threads.value[0]) select(threads.value[0].id)
  } catch (e) {
    error.value = e?.response?.data?.message || e.message || 'Error'
  } finally { loading.value = false }
}

async function select(id) {
  activeId.value = id
  activeThread.value = threads.value.find(t => t.id === id) || null
  try {
    const res = await api.get(`/messages/threads/${id}`)
    threadDetail.value = res.data?.data || null
  } catch (e) { threadDetail.value = null }
  if (!messages.value[id]) messages.value[id] = await listMessages(id)
}

async function onSend() {
  if (!activeId.value || !draft.value.trim()) return
  sending.value = true
  try {
    const m = await sendMessage(activeId.value, draft.value.trim())
    messages.value[activeId.value] = [...(messages.value[activeId.value] || []), m]
    draft.value = ''
  } finally { sending.value = false }
}

function openNewMessage() {
  newForm.value = { subject: '', provider_id: null, initial_message: '' }
  modals.value.newMessage = true
}

async function createNewThread() {
  creating.value = true
  try {
    const th = await createThread({ ...newForm.value })
    modals.value.newMessage = false
    await load()
    select(th.id)
  } catch (e) {
    // no-op minimal
  } finally { creating.value = false }
}

function formatDate(iso) { try { return new Date(iso).toLocaleString() } catch { return '' } }
</script>
