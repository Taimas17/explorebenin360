import api from '@/lib/api'
import type { Review, ReviewsResponse } from '@/types/business'

export async function fetchReviews(params: {
  reviewable_type: string
  reviewable_id: number
  verified_only?: boolean
  sort?: 'recent' | 'rating' | 'helpful'
  page?: number
}): Promise<ReviewsResponse> {
  const res = await api.get('/reviews', { params })
  return res.data
}

export async function createReview(data: {
  reviewable_type: string
  reviewable_id: number
  booking_id?: number
  rating: number
  title?: string
  body: string
}): Promise<Review> {
  const res = await api.post('/reviews', data)
  return res.data.data
}

export async function updateReview(id: number, data: Partial<Review>): Promise<Review> {
  const res = await api.patch(`/reviews/${id}`, data)
  return res.data.data
}

export async function deleteReview(id: number): Promise<void> {
  await api.delete(`/reviews/${id}`)
}

export async function respondToReview(id: number, response: string): Promise<Review> {
  const res = await api.post(`/reviews/${id}/respond`, { response })
  return res.data.data
}

export async function markHelpful(id: number): Promise<number> {
  const res = await api.post(`/reviews/${id}/helpful`)
  return res.data.helpful_count
}

export async function fetchAdminReviews(filters?: any) {
  const res = await api.get('/admin/reviews', { params: filters })
  return res.data
}

export async function approveReview(id: number): Promise<void> {
  await api.patch(`/admin/reviews/${id}/approve`)
}

export async function rejectReview(id: number, reason: string): Promise<void> {
  await api.patch(`/admin/reviews/${id}/reject`, { reason })
}
