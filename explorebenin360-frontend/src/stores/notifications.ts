import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { 
  fetchNotifications, 
  markNotificationAsRead, 
  markAllNotificationsAsRead 
} from '@/lib/api'

export type Notification = {
  id: string
  type: string
  data: any
  read_at: string | null
  created_at: string
}

export const useNotificationsStore = defineStore('notifications', () => {
  const notifications = ref<Notification[]>([])
  const unreadCount = ref(0)
  const loading = ref(false)

  const unreadNotifications = computed(() => 
    notifications.value.filter(n => !n.read_at)
  )

  async function fetch() {
    loading.value = true
    try {
      const res = await fetchNotifications()
      notifications.value = res.data as any
      unreadCount.value = (res as any).meta.unread_count
    } finally {
      loading.value = false
    }
  }

  async function markAsRead(id: string) {
    await markNotificationAsRead(id)
    const notif = notifications.value.find(n => n.id === id)
    if (notif) {
      notif.read_at = new Date().toISOString()
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  }

  async function markAllAsRead() {
    await markAllNotificationsAsRead()
    notifications.value.forEach(n => {
      if (!n.read_at) n.read_at = new Date().toISOString()
    })
    unreadCount.value = 0
  }

  function startPolling(intervalMs = 30000) {
    const interval = setInterval(() => fetch(), intervalMs)
    return () => clearInterval(interval)
  }

  return {
    notifications,
    unreadCount,
    unreadNotifications,
    loading,
    fetch,
    markAsRead,
    markAllAsRead,
    startPolling
  }
})
