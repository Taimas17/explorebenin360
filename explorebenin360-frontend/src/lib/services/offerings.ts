import api from '@/lib/api'
import type { Offering } from '@/types/business'

const useStubs = (import.meta.env.VITE_USE_STUBS === 'true')
const delay = (ms = 300) => new Promise((r) => setTimeout(r, ms))

let offeringId = 100

const stubOfferings: Offering[] = Array.from({ length: 4 }).map((_, i) => ({
  id: offeringId + i,
  title: `Excursion Ganvié ${i + 1}`,
  slug: `excursion-ganvie-${i + 1}`,
  description: 'Balade inoubliable sur le lac Nokoué et visite du village lacustre de Ganvié.',
  price: 35000 + i * 5000,
  currency: 'XOF',
  capacity: 6,
  cover_image_url: '/src/assets/brand/images/thumbs/destination-thumb.png',
  gallery: [
    { src: '/src/assets/brand/images/home/hero-1.png', alt: 'Photo 1' },
    { src: '/src/assets/brand/images/home/hero-2.png', alt: 'Photo 2' },
  ],
  status: i % 2 === 0 ? 'published' : 'draft',
  availability_json: {
    blocks: ['2025-10-29','2025-11-05'],
    weekdays: { 1: true, 2: true, 3: true, 4: false, 5: true, 6: true, 0: false }
  }
}))

export async function providerOfferings(): Promise<Offering[]> {
  if (useStubs) { await delay(); return stubOfferings }
  const res = await api.get('/provider/offerings')
  return res.data.data ?? res.data
}

export async function createOffering(payload: Partial<Offering>): Promise<Offering> {
  if (useStubs) {
    await delay()
    const o: Offering = { id: ++offeringId, title: payload.title || 'Nouvelle offre', slug: `offre-${offeringId}`, price: payload.price || 0, currency: payload.currency || 'XOF', status: payload.status || 'draft', description: payload.description, cover_image_url: payload.cover_image_url, gallery: payload.gallery || [], capacity: payload.capacity }
    stubOfferings.unshift(o)
    return o
  }
  const res = await api.post('/provider/offerings', payload)
  return res.data
}

export async function updateOffering(id: number, payload: Partial<Offering>): Promise<Offering> {
  if (useStubs) {
    await delay()
    const idx = stubOfferings.findIndex(o => o.id === id)
    if (idx >= 0) stubOfferings[idx] = { ...stubOfferings[idx], ...payload }
    return stubOfferings[idx]
  }
  const res = await api.patch(`/provider/offerings/${id}`, payload)
  return res.data
}

export async function fetchOfferingById(id: number): Promise<Offering> {
  if (useStubs) { await delay(); return stubOfferings.find(o => o.id === id)! }
  const res = await api.get(`/provider/offerings/${id}`)
  return res.data
}
