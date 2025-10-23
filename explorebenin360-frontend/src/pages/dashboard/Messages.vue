<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" alt="Bannière Messages" :title="t('dashboard.messages')" class="mb-6" />

    <div class="grid md:grid-cols-3 gap-4">
      <div class="md:col-span-1 border border-black/10 dark:border-white/10 rounded-md overflow-hidden">
        <div class="px-3 py-2 text-xs text-[color:var(--color-text-muted)] flex items-center justify-between">
          <span>{{ t('dashboard.threads') }}</span>
          <span class="inline-flex items-center gap-1"><Icon name="Mail" class="h-4 w-4"/> {{ totalUnread }}</span>
        </div>
        <ul class="divide-y divide-black/10 dark:divide-white/10">
          <li v-for="th in threads" :key="th.id">
            <button @click="select(th.id)" class="w-full text-left px-3 py-2 hover:bg-black/5 dark:hover:bg-white/5 focus-ring">
              <div class="flex items-center justify-between">
                <div class="font-medium text-sm truncate">{{ th.subject }}</div>
                <span v-if="th.unread_count" class="inline-flex items-center justify-center text-[10px] min-w-5 h-5 rounded-full bg-[color:var(--color-secondary)]/20 text-[color:var(--color-secondary)] px-1">{{ th.unread_count }}</span>
              </div>
              <div class="text-xs text-[color:var(--color-text-muted)] truncate">{{ th.last_message_preview }}</div>
            </button>
          </li>
        </ul>
      </div>

      <div class="md:col-span-2 border border-black/10 dark:border-white/10 rounded-md p-3 flex flex-col">
        <div v-if="activeMessages.length === 0" class="grow grid place-items-center py-8">
          <EmptyState variant="default" :title="t('dashboard.pick_thread')" />
        </div>
        <div v-else class="grow space-y-3 overflow-y-auto max-h-[50vh] pr-2">
          <div v-for="m in activeMessages" :key="m.id" class="rounded-md border border-black/10 dark:border-white/10 p-2">
            <div class="text-xs text-[color:var(--color-text-muted)] mb-1">{{ m.author.name }} · {{ new Date(m.created_at).toLocaleString() }}</div>
            <div class="text-sm">{{ m.body }}</div>
          </div>
        </div>
        <form class="mt-3 flex gap-2" @submit.prevent="onSend" aria-label="{{ t('dashboard.send_message') }}">
          <input v-model="draft" class="flex-1 rounded-md border border-black/10 dark:border-white/10 px-3 py-2 bg-transparent focus-ring" :placeholder="t('dashboard.type_message')" />
          <button :disabled="!activeId || !draft.trim() || sending" class="btn-base focus-ring h-10 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
            <span v-if="sending"><Loader /></span>
            <span v-else>{{ t('dashboard.send') }}</span>
          </button>
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
import { listThreads, listMessages, sendMessage } from '@/lib/services/messages'

const { t } = useI18n()
const banner = '/src/assets/brand/images/dashboard/traveler/header.png'

const threads = ref([])
const activeId = ref<number | null>(null)
const messages = ref<Record<number, any[]>>({})
const draft = ref('')
const sending = ref(false)

const totalUnread = computed(() => threads.value.reduce((s, th) => s + (th.unread_count || 0), 0))
const activeMessages = computed(() => (activeId.value ? (messages.value[activeId.value] || []) : []))

onMounted(async () => {
  threads.value = await listThreads()
  if (threads.value[0]) select(threads.value[0].id)
})

async function select(id: number) {
  activeId.value = id
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
</script>
