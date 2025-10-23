import api from '@/lib/api'
import type { ProviderUser } from '@/types/business'

const useStubs = (import.meta.env.VITE_USE_STUBS === 'true')
const delay = (ms = 300) => new Promise((r) => setTimeout(r, ms))

const stubProviders: ProviderUser[] = [
  { id: 1, name: 'Agence Bénin Aventures', email: 'contact@beninaventures.bj', phone: '+229 62 00 11 22', status: 'pending', kyc_submitted: true, kyc_verified: false },
  { id: 2, name: 'Hôtel Cotonou Centre', email: 'hello@hotelcc.bj', phone: '+229 62 00 33 44', status: 'approved', kyc_submitted: true, kyc_verified: true },
  { id: 3, name: 'Guide Akotonon', email: 'akotonon@guide.bj', phone: '+229 62 00 55 66', status: 'pending', kyc_submitted: false, kyc_verified: false },
]

export async function listProviders(params: { status?: string } = {}): Promise<ProviderUser[]> {
  if (useStubs) { await delay(); return params.status ? stubProviders.filter(p => p.status === params.status) : stubProviders }
  const res = await api.get('/admin/providers', { params })
  return res.data.data ?? res.data
}

export async function approveProvider(id: number): Promise<void> {
  if (useStubs) { await delay(); const p = stubProviders.find(x => x.id === id); if (p) p.status = 'approved'; return }
  await api.patch(`/admin/providers/${id}/approve`)
}

export async function rejectProvider(id: number): Promise<void> {
  if (useStubs) { await delay(); const p = stubProviders.find(x => x.id === id); if (p) p.status = 'rejected'; return }
  await api.patch(`/admin/providers/${id}/reject`)
}
