import api from '@/lib/api'
import type { Booking, BookingStatus } from '@/types/business'

const useStubs = (import.meta.env.VITE_USE_STUBS === 'true')

const delay = (ms = 300) => new Promise((r) => setTimeout(r, ms))

const stubBookings: Booking[] = Array.from({ length: 6 }).map((_, i) => ({
  id: i + 1,
  offering: { id: 10 + i, title: `Séjour au Lac Nokoué ${i + 1}`, slug: `sejour-lac-${i + 1}` },
  user: { id: 1, name: 'John Doe', email: 'john@example.com' },
  start_date: new Date(Date.now() + (i - 2) * 86400000).toISOString().slice(0, 10),
  end_date: new Date(Date.now() + (i - 1) * 86400000).toISOString().slice(0, 10),
  guests: 2 + (i % 3),
  status: (['pending', 'authorized', 'confirmed', 'cancelled'] as BookingStatus[])[i % 4],
  currency: 'XOF',
  amount: 45000 + i * 5000,
  commission_amount: 4500 + i * 500,
  receipt_url: i % 2 === 0 ? `https://example.com/receipt/${i + 1}` : null,
  created_at: new Date(Date.now() - i * 3600000).toISOString(),
}))

export async function travelerBookings(): Promise<Booking[]> {
  if (useStubs) { await delay(); return stubBookings }
  const res = await api.get('/bookings')
  const d = res.data
  if (Array.isArray(d?.data?.data)) return d.data.data
  if (Array.isArray(d?.data)) return d.data
  return d
}

export async function providerBookingsService(params: { status?: string; from?: string; to?: string } = {}): Promise<Booking[]> {
  if (useStubs) { await delay(); return stubBookings }
  const res = await api.get('/provider/bookings', { params })
  const d = res.data
  if (Array.isArray(d?.data?.data)) return d.data.data
  if (Array.isArray(d?.data)) return d.data
  return d
}

export async function adminBookingsService(params: { status?: string; from?: string; to?: string } = {}): Promise<Booking[]> {
  if (useStubs) { await delay(); return stubBookings }
  const res = await api.get('/admin/bookings', { params })
  const d = res.data
  if (Array.isArray(d?.data?.data)) return d.data.data
  if (Array.isArray(d?.data)) return d.data
  return d
}

export async function fetchBookingService(id: number): Promise<Booking> {
  if (useStubs) { await delay(); return stubBookings.find(b => b.id === id)! }
  const res = await api.get(`/bookings/${id}`)
  return res.data
}

export async function cancelBookingService(id: number): Promise<void> {
  if (useStubs) { await delay(); return }
  await api.post(`/bookings/${id}/cancel`)
}

export async function adminUpdateBookingStatus(id: number, status: BookingStatus): Promise<void> {
  if (useStubs) { await delay(); return }
  await api.patch(`/admin/bookings/${id}`, { status })
}
