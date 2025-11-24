import api from '@/lib/api'
import type { Accommodation, Article, Event, Guide, Place } from '@/types/content'

export async function fetchAdminAccommodations(filters?: any) {
  const res = await api.get('/admin/accommodations', { params: filters })
  return res.data
}
export async function createAccommodation(data: Partial<Accommodation>) {
  const res = await api.post('/admin/accommodations', data)
  return res.data
}
export async function getAccommodation(id: number) {
  const res = await api.get(`/admin/accommodations/${id}`)
  return res.data
}
export async function updateAccommodation(id: number, data: Partial<Accommodation>) {
  const res = await api.patch(`/admin/accommodations/${id}`, data)
  return res.data
}
export async function deleteAccommodation(id: number) {
  await api.delete(`/admin/accommodations/${id}`)
}

export async function fetchAdminArticles(filters?: any) {
  const res = await api.get('/admin/articles', { params: filters })
  return res.data
}
export async function createArticle(data: Partial<Article>) {
  const res = await api.post('/admin/articles', data)
  return res.data
}
export async function getArticle(id: number) {
  const res = await api.get(`/admin/articles/${id}`)
  return res.data
}
export async function updateArticle(id: number, data: Partial<Article>) {
  const res = await api.patch(`/admin/articles/${id}`, data)
  return res.data
}
export async function deleteArticle(id: number) {
  await api.delete(`/admin/articles/${id}`)
}

export async function fetchAdminEvents(filters?: any) {
  const res = await api.get('/admin/events', { params: filters })
  return res.data
}
export async function createEvent(data: Partial<Event>) {
  const res = await api.post('/admin/events', data)
  return res.data
}
export async function getEvent(id: number) {
  const res = await api.get(`/admin/events/${id}`)
  return res.data
}
export async function updateEvent(id: number, data: Partial<Event>) {
  const res = await api.patch(`/admin/events/${id}`, data)
  return res.data
}
export async function deleteEvent(id: number) {
  await api.delete(`/admin/events/${id}`)
}

export async function fetchAdminGuides(filters?: any) {
  const res = await api.get('/admin/guides', { params: filters })
  return res.data
}
export async function createGuide(data: Partial<Guide>) {
  const res = await api.post('/admin/guides', data)
  return res.data
}
export async function getGuide(id: number) {
  const res = await api.get(`/admin/guides/${id}`)
  return res.data
}
export async function updateGuide(id: number, data: Partial<Guide>) {
  const res = await api.patch(`/admin/guides/${id}`, data)
  return res.data
}
export async function deleteGuide(id: number) {
  await api.delete(`/admin/guides/${id}`)
}

export async function fetchAdminPlaces(filters?: any) {
  const res = await api.get('/admin/places', { params: filters })
  return res.data
}
export async function createPlace(data: Partial<Place>) {
  const res = await api.post('/admin/places', data)
  return res.data
}
export async function getPlace(id: number) {
  const res = await api.get(`/admin/places/${id}`)
  return res.data
}
export async function updatePlace(id: number, data: Partial<Place>) {
  const res = await api.patch(`/admin/places/${id}`, data)
  return res.data
}
export async function deletePlace(id: number) {
  await api.delete(`/admin/places/${id}`)
}
