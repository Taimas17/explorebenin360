import { defineStore } from 'pinia'
import { providerBookingsService, adminBookingsService, travelerBookings, adminUpdateBookingStatus, fetchBookingService } from '@/lib/services/bookings'
import { providerUpdateBookingStatus } from '@/lib/services/bookings'
import type { Booking, BookingStatus } from '@/types/business'

export const useBookingsStore = defineStore('bookings', {
  state: () => ({ items: [] as Booking[], loading: false, error: null as string | null }),
  actions: {
    async loadTraveler() {
      this.loading = true; this.error = null
      try { this.items = await travelerBookings() } catch (e: any) { this.error = e?.message || 'Error' } finally { this.loading = false }
    },
    async loadProvider(params: { status?: string; from?: string; to?: string } = {}) {
      this.loading = true; this.error = null
      try { this.items = await providerBookingsService(params) } catch (e: any) { this.error = e?.message || 'Error' } finally { this.loading = false }
    },
    async loadAdmin(params: { status?: string; from?: string; to?: string } = {}) {
      this.loading = true; this.error = null
      try { this.items = await adminBookingsService(params) } catch (e: any) { this.error = e?.message || 'Error' } finally { this.loading = false }
    },
    async refreshOne(id: number) {
      const updated = await fetchBookingService(id)
      const i = this.items.findIndex(b => b.id === id)
      if (i >= 0) this.items[i] = updated
    },
    async providerSetStatus(id: number, status: Extract<BookingStatus, 'authorized'|'cancelled'>) {
      const idx = this.items.findIndex(b => b.id === id)
      const prev = idx >= 0 ? { ...this.items[idx] } : null
      if (idx >= 0) this.items[idx] = { ...this.items[idx], status }
      try { await providerUpdateBookingStatus(id, status) }
      catch (e) { if (idx >= 0 && prev) this.items[idx] = prev; throw e }
    },
    async adminSetStatus(id: number, status: BookingStatus) {
      const idx = this.items.findIndex(b => b.id === id)
      const prev = idx >= 0 ? { ...this.items[idx] } : null
      if (idx >= 0) this.items[idx] = { ...this.items[idx], status }
      try { await adminUpdateBookingStatus(id, status) }
      catch (e) { if (idx >= 0 && prev) this.items[idx] = prev; throw e }
    }
  }
})
