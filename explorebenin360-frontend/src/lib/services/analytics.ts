import type { Booking } from '@/types/business'

export type KPI = { label: string; value: number; suffix?: string }
export type TimePoint = { date: string; value: number }

export function computeProviderKPIs(bookings: Booking[]): KPI[] {
  const confirmed = bookings.filter(b => b.status === 'confirmed')
  const total = bookings.length
  const totalConfirmed = confirmed.length
  const gross = confirmed.reduce((s, b) => s + Number(b.amount || 0), 0)
  const commission = confirmed.reduce((s, b) => s + Number(b.commission_amount || 0), 0)
  const net = gross - commission
  return [
    { label: 'total_bookings', value: total },
    { label: 'confirmed_bookings', value: totalConfirmed },
    { label: 'gross_amount', value: gross },
    { label: 'commission_total', value: commission },
    { label: 'net_earnings', value: net },
  ]
}

export function buildTimeseries(days: number, seed = 42): TimePoint[] {
  const rng = mulberry32(seed)
  const now = new Date()
  const data: TimePoint[] = []
  for (let i = days - 1; i >= 0; i--) {
    const d = new Date(now)
    d.setDate(now.getDate() - i)
    const value = Math.max(0, Math.round((rng() * 0.6 + 0.2) * 1000))
    data.push({ date: d.toISOString().slice(0, 10), value })
  }
  return data
}

function mulberry32(a: number) {
  return function() {
    let t = (a += 0x6D2B79F5)
    t = Math.imul(t ^ (t >>> 15), t | 1)
    t ^= t + Math.imul(t ^ (t >>> 7), t | 61)
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296
  }
}
