import type { Thread, Message } from '@/types/business'

const useStubs = (import.meta.env.VITE_USE_STUBS === 'true')
const delay = (ms = 200) => new Promise((r) => setTimeout(r, ms))

const stubThreads: Thread[] = [
  { id: 1, subject: 'Infos sur l’excursion Ganvié', unread_count: 2, last_message_preview: 'Bonjour, est-ce possible dimanche ?', updated_at: new Date().toISOString() },
  { id: 2, subject: 'Disponibilité chambre double', unread_count: 0, last_message_preview: 'Merci pour votre réponse.', updated_at: new Date(Date.now() - 86400000).toISOString() },
]

const stubMessages: Message[] = [
  { id: 1, thread_id: 1, author: { id: 2, name: 'Agence Bénin Aventures' }, body: 'Bonjour, oui dimanche 10h est possible.', created_at: new Date().toISOString() },
  { id: 2, thread_id: 1, author: { id: 1, name: 'Vous' }, body: 'Parfait, merci !', created_at: new Date().toISOString() },
]

export async function listThreads(): Promise<Thread[]> {
  if (useStubs) { await delay(); return stubThreads }
  // TODO: replace with GET /messages/threads
  await delay();
  return []
}

export async function listMessages(threadId: number): Promise<Message[]> {
  if (useStubs) { await delay(); return stubMessages.filter(m => m.thread_id === threadId) }
  // TODO: replace with GET /messages/threads/:id
  await delay();
  return []
}

export async function sendMessage(threadId: number, body: string): Promise<Message> {
  if (useStubs) { await delay(); const m: Message = { id: Date.now(), thread_id: threadId, author: { id: 1, name: 'Vous' }, body, created_at: new Date().toISOString() }; stubMessages.push(m); return m }
  // TODO: replace with POST /messages/threads/:id
  await delay();
  return { id: Date.now(), thread_id: threadId, author: { id: 1, name: 'You' }, body, created_at: new Date().toISOString() }
}
