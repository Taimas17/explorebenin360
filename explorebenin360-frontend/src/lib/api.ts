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
const del = async <T>(url: string) => {
  const { data } = await api.delete<T>(url)
  return data
}
const patch = async <T>(url: string, body?: any) => {
  const { data } = await api.patch<T>(url, body)
  return data
}

export const fetchPlaces = (filters: PlaceFilters = {}) => get<Paginated<Place>>('/places', filters)
export const fetchPlace = (slug: string) => get<{ data: Place }>(`/places/${slug}`)
export const fetchPlaceById = (id: number) => get<{ data: Place }>(`/places/${id}`)

export const fetchAccommodations = (filters: AccommodationFilters = {}) => get<Paginated<Accommodation>>('/accommodations', filters)
export const fetchAccommodation = (slug: string) => get<{ data: Accommodation }>(`/accommodations/${slug}`)
export const fetchAccommodationById = (id: number) => get<{ data: Accommodation }>(`/accommodations/${id}`)

export const fetchGuides = (filters: GuideFilters = {}) => get<Paginated<Guide>>('/guides', filters)
export const fetchGuide = (slug: string) => get<{ data: Guide }>(`/guides/${slug}`)
export const fetchGuideById = (id: number) => get<{ data: Guide }>(`/guides/${id}`)

export const fetchArticles = (filters: ArticleFilters = {}) => get<Paginated<Article>>('/articles', filters)
export const fetchArticle = (slug: string) => get<{ data: Article }>(`/articles/${slug}`)
export const fetchArticleById = (id: number) => get<{ data: Article }>(`/articles/${id}`)

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

// Favorites (graceful 404 fallback expected by callers)
export const getFavorites = () => get<{ data: { destination: number[]; hebergement: number[]; article: number[]; guide: number[] } }>('/favorites')
export const addFavorite = (type: 'destination'|'hebergement'|'article'|'guide', id: number) => post('/favorites', { type, id })
export const removeFavorite = (type: 'destination'|'hebergement'|'article'|'guide', id: number) => post('/favorites/remove', { type, id })

// Provider Application
export const applyAsProvider = (body: { business_name: string; phone: string; bio: string; kyc_documents?: string[] }) => 
  post('/provider/apply', body)
export const fetchProviderStatus = () => get<{ data: any }>('/provider/status')

// Provider Offerings Management
export const fetchProviderOfferings = (params?: { status?: string }) => 
  get<{ data: any[] }>('/provider/offerings', params)
export const createProviderOffering = (body: any) => post('/provider/offerings', body)
export const fetchProviderOffering = (id: number) => get<{ data: any }>(`/provider/offerings/${id}`)
export const updateProviderOffering = (id: number, body: any) => patch(`/provider/offerings/${id}`, body)
export const deleteProviderOffering = (id: number) => del(`/provider/offerings/${id}`)
export const updateOfferingAvailability = (id: number, availability: any) => 
  patch(`/provider/offerings/${id}/availability`, { availability })

// Provider Analytics
export const fetchProviderAnalytics = () => get<{ data: any }>('/provider/analytics')

// Content Moderation
export const reportContent = (body: {
  reportable_type: string
  reportable_id: number
  reason: string
  description?: string
}) => post('/content/report', body)

export const fetchModerationReports = (status = 'pending') =>
  get<{ data: any[] }>('/admin/moderation/reports', { status })

export const resolveReport = (id: number, action: 'remove' | 'flag' | 'warn', note?: string) =>
  post(`/admin/moderation/reports/${id}/resolve`, { action, note })

export const dismissReport = (id: number, note?: string) =>
  post(`/admin/moderation/reports/${id}/dismiss`, { note })

export const fetchFlaggedContent = (type?: string) =>
  get<{ data: any[] }>('/admin/moderation/flagged-content', type ? { type } : {})

export const unflagContent = (type: string, id: number) =>
  post('/admin/moderation/unflag', { type, id })

export default api
