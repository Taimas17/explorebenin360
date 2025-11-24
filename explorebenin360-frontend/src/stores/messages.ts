import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { MessageThread, Message } from '@/types/business'
import { listThreads, listMessages, sendMessage } from '@/lib/services/messages'

export const useMessagesStore = defineStore('messages', () => {
  const threads = ref<MessageThread[]>([])
  const currentThread = ref<MessageThread | null>(null)
  const currentMessages = ref<Message[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function loadThreads() {
    loading.value = true
    error.value = null
    try {
      threads.value = await listThreads()
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  async function loadThread(id: number) {
    loading.value = true
    error.value = null
    try {
      const res = await listMessages(id)
      currentMessages.value = res
      currentThread.value = threads.value.find(t => t.id === id) || null
    } catch (e: any) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  async function sendReply(threadId: number, body: string) {
    try {
      const newMsg = await sendMessage(threadId, body)
      currentMessages.value.push(newMsg)
    } catch (e: any) {
      error.value = e.message
      throw e
    }
  }

  return {
    threads,
    currentThread,
    currentMessages,
    loading,
    error,
    loadThreads,
    loadThread,
    sendReply,
  }
})
