import api from '@/lib/api'
import type { Review } from '@/types/business'

export async function fetchReviews(params: {
  reviewable_type: 'accommodations' | 'guides' | 'places'
  reviewable_id: number
  per_page?: number
}): Promise<{ data: Review[], meta: any }> {
  const res = await api.get('/reviews', { params })
  return { data: res.data.data, meta: res.data.meta }
}

export async function fetchMyReviews(): Promise<Review[]> {
  const res = await api.get('/reviews/my')
  return res.data.data || []
}

export async function canReviewBooking(bookingId: number): Promise<{ can_review: boolean, reason?: string, review_id?: number }> {
  const res = await api.get(`/reviews/can-review/${bookingId}`)
  return res.data
}

export async function createReview(payload: {
  booking_id: number
  rating: number
  comment?: string
}): Promise<Review> {
  const res = await api.post('/reviews', payload)
  return res.data.data
}

export async function updateReview(id: number, payload: {
  rating: number
  comment?: string
}): Promise<Review> {
  const res = await api.patch(`/reviews/${id}`, payload)
  return res.data.data
}

export async function deleteReview(id: number): Promise<void> {
  await api.delete(`/reviews/${id}`)
}

export async function fetchAdminReviews(params?: {
  status?: 'pending' | 'approved' | 'rejected'
  per_page?: number
}): Promise<{ data: Review[], meta: any }> {
  const res = await api.get('/admin/reviews', { params })
  return { data: res.data.data, meta: res.data.meta }
}

export async function approveReview(id: number): Promise<void> {
  await api.patch(`/admin/reviews/${id}/approve`)
}

export async function rejectReview(id: number): Promise<void> {
  await api.patch(`/admin/reviews/${id}/reject`)
}

export async function deleteReviewAdmin(id: number): Promise<void> {
  await api.delete(`/admin/reviews/${id}`)
}
