import axios from 'axios'
import type { Paginated } from '@/types/pagination'
import type { Place, Accommodation, Guide, Article, Event } from '@/types/content'
import type { PlaceFilters, AccommodationFilters, GuideFilters, ArticleFilters, EventFilters } from '@/types/filters'

const api = axios.create({ baseURL: import.meta.env.VITE_API_BASE_URL || '/api/v1' })

export const setAuthToken = (token?: string | null) => {
  if (token) api.defaults.headers.common['Authorization'] = `Bearer ${token}`
  else delete api.defaults.headers.common['Authorization']
}

api.interceptors.response.use(
  (res) => res,
  (err) => {
    return Promise.reject(err)
  }
)

const get = async <T>(url: string, params?: Record<string, any>) => {
  const { data } = await api.get<T>(url, { params })
  return data
}
const post = async <T>(url: string, body?: any) => {
  const { data } = await api.post<T>(url, body)
  return data
}
const patch = async <T>(url: string, body?: any) => {
  const { data } = await api.patch<T>(url, body)
  return data
}

export const fetchPlaces = (filters: PlaceFilters = {}) => get<Paginated<Place>>('/places', filters)
export const fetchPlace = (slug: string) => get<{ data: Place }>(`/places/${slug}`)

export const fetchAccommodations = (filters: AccommodationFilters = {}) => get<Paginated<Accommodation>>('/accommodations', filters)
export const fetchAccommodation = (slug: string) => get<{ data: Accommodation }>(`/accommodations/${slug}`)

export const fetchGuides = (filters: GuideFilters = {}) => get<Paginated<Guide>>('/guides', filters)
export const fetchGuide = (slug: string) => get<{ data: Guide }>(`/guides/${slug}`)

export const fetchArticles = (filters: ArticleFilters = {}) => get<Paginated<Article>>('/articles', filters)
export const fetchArticle = (slug: string) => get<{ data: Article }>(`/articles/${slug}`)

export const fetchEvents = (filters: EventFilters = {}) => get<Paginated<Event>>('/events', filters)
export const fetchEvent = (slug: string) => get<{ data: Event }>(`/events/${slug}`)

// Auth
export const apiRegister = (body: { name: string; email: string; password: string }) => post('/auth/register', body)
export const apiLogin = (body: { email: string; password: string }) => post('/auth/login', body)
export const apiLogout = () => post('/auth/logout')
export const apiMe = () => get('/auth/me')

// Offerings & Bookings
export const fetchOfferings = (filters: any = {}) => get('/offerings', filters)
export const fetchOffering = (slug: string) => get(`/offerings/${slug}`)
export const createCheckoutSession = (body: { offering_id: number; start_date: string; end_date?: string; guests?: number }) => post('/checkout/session', body)
export const fetchBooking = (id: number) => get(`/bookings/${id}`)
export const cancelBooking = (id: number) => post(`/bookings/${id}/cancel`)
export const providerBookings = () => get('/provider/bookings')
export const adminBookings = (filters: any = {}) => get('/admin/bookings', filters)
export const adminUpdateBooking = (id: number, body: any) => patch(`/admin/bookings/${id}`, body)

export default api
