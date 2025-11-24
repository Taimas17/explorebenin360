import api from '@/lib/api'
import type { MessageThread, Message } from '@/types/business'

const useStubs = (import.meta.env.VITE_USE_STUBS === 'true')
const delay = (ms = 200) => new Promise((r) => setTimeout(r, ms))

const stubThreads: MessageThread[] = [
  { id: 1, subject: 'Infos sur l’excursion Ganvié', status: 'open', traveler: { id: 1, name: 'Vous' }, provider: { id: 2, name: 'Agence', business_name: 'Bénin Aventures' }, booking: null, offering: null, unread_count: 2, last_message_preview: 'Bonjour, est-ce possible dimanche ?', last_message_at: new Date().toISOString(), created_at: new Date().toISOString() },
  { id: 2, subject: 'Disponibilité chambre double', status: 'open', traveler: { id: 1, name: 'Vous' }, provider: { id: 3, name: 'Hôtel', business_name: 'Cotonou Suites' }, booking: null, offering: null, unread_count: 0, last_message_preview: 'Merci pour votre réponse.', last_message_at: new Date(Date.now() - 86400000).toISOString(), created_at: new Date(Date.now() - 86400000 * 2).toISOString() },
]

const stubMessages: Message[] = [
  { id: 1, thread_id: 1, sender: { id: 2, name: 'Agence Bénin Aventures' }, body: 'Bonjour, oui dimanche 10h est possible.', read_at: null, created_at: new Date().toISOString() },
  { id: 2, thread_id: 1, sender: { id: 1, name: 'Vous' }, body: 'Parfait, merci !', read_at: new Date().toISOString(), created_at: new Date().toISOString() },
]

export async function listThreads(): Promise<MessageThread[]> {
  if (useStubs) { await delay(); return stubThreads }
  const res = await api.get('/messages/threads')
  const d = res.data
  if (Array.isArray(d?.data?.data)) return d.data.data
  if (Array.isArray(d?.data)) return d.data
  return d
}

export async function listMessages(threadId: number): Promise<Message[]> {
  if (useStubs) { await delay(); return stubMessages.filter(m => m.thread_id === threadId) }
  const res = await api.get(`/messages/threads/${threadId}`)
  return res.data?.data?.messages || []
}

export async function sendMessage(threadId: number, body: string): Promise<Message> {
  if (useStubs) {
    await delay();
    const m: Message = { id: Date.now(), thread_id: threadId, sender: { id: 1, name: 'Vous' }, body, read_at: null, created_at: new Date().toISOString() };
    stubMessages.push(m);
    return m
  }
  const res = await api.post(`/messages/threads/${threadId}`, { body })
  return res.data.data
}

export async function createThread(payload: {
  subject: string
  provider_id: number
  booking_id?: number
  offering_id?: number
  initial_message: string
}): Promise<MessageThread> {
  const res = await api.post('/messages/threads', payload)
  return res.data.data
}
