<template>
  <div class="relative">
    <button
      @click="togglePanel"
      class="relative focus-ring rounded-full p-2 hover:bg-black/5 dark:hover:bg-white/5"
      :aria-label="t('notifications.title')"
    >
      <Icon name="Bell" class="h-6 w-6" />
      <span
        v-if="store.unreadCount > 0"
        class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 min-w-[20px] flex items-center justify-center px-1"
      >
        {{ store.unreadCount > 99 ? '99+' : store.unreadCount }}
      </span>
    </button>

    <div
      v-if="showPanel"
      v-click-outside="closePanel"
      class="absolute right-0 top-full mt-2 w-96 max-w-[calc(100vw-2rem)] max-h-[600px] bg-white dark:bg-gray-900 border border-black/10 dark:border-white/10 rounded-lg shadow-lg z-50 overflow-hidden"
    >
      <div class="px-4 py-3 border-b border-black/10 dark:border-white/10 flex items-center justify-between">
        <h3 class="font-semibold">{{ t('notifications.title') }}</h3>
        <button
          v-if="store.unreadCount > 0"
          @click="handleMarkAllAsRead"
          class="text-xs text-blue-500 hover:underline"
        >
          {{ t('notifications.mark_all_read') }}
        </button>
      </div>

      <div class="overflow-y-auto max-h-[500px]">
        <div v-if="store.loading" class="text-center py-8">
          <Loader />
        </div>

        <EmptyState
          v-else-if="store.notifications.length === 0"
          variant="default"
          :title="t('notifications.no_notifications')"
        />

        <div v-else>
          <div
            v-for="notif in store.notifications"
            :key="notif.id"
            @click="handleNotificationClick(notif)"
            :class="[
              'px-4 py-3 border-b border-black/10 dark:border-white/10 cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition',
              !notif.read_at && 'bg-blue-50 dark:bg-blue-900/20'
            ]"
          >
            <div class="flex items-start gap-3">
              <div
                v-if="!notif.read_at"
                class="w-2 h-2 rounded-full bg-blue-500 mt-1 shrink-0"
              />
              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium">{{ notif.data.message }}</div>
                <div class="text-xs text-[color:var(--color-text-muted)] mt-1">
                  {{ formatDate(notif.created_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useNotificationsStore } from '@/stores/notifications'
import Icon from './Icon.vue'
import Loader from './Loader.vue'
import EmptyState from './EmptyState.vue'

const { t } = useI18n()
const router = useRouter()
const store = useNotificationsStore()

const showPanel = ref(false)
let stopPolling: (() => void) | null = null

onMounted(() => {
  store.fetch()
  stopPolling = store.startPolling()
})

onUnmounted(() => {
  if (stopPolling) stopPolling()
})

function togglePanel() {
  showPanel.value = !showPanel.value
}

function closePanel() {
  showPanel.value = false
}

async function handleNotificationClick(notif: any) {
  if (!notif.read_at) {
    await store.markAsRead(notif.id)
  }
  
  if (notif.data.action_url) {
    router.push(notif.data.action_url)
  }
  
  closePanel()
}

async function handleMarkAllAsRead() {
  await store.markAllAsRead()
}

function formatDate(date: string): string {
  const d = new Date(date)
  const now = new Date()
  const diffMs = now.getTime() - d.getTime()
  const diffMins = Math.floor(diffMs / 60000)
  
  if (diffMins < 1) return "À l'instant"
  if (diffMins < 60) return `Il y a ${diffMins}min`
  if (diffMins < 1440) return `Il y a ${Math.floor(diffMins / 60)}h`
  if (diffMins < 10080) return `Il y a ${Math.floor(diffMins / 1440)}j`
  
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
}
</script>
