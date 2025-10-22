import axios from 'axios'
import type { Paginated } from '@/types/pagination'
import type { Place, Accommodation, Guide, Article, Event } from '@/types/content'
import type { PlaceFilters, AccommodationFilters, GuideFilters, ArticleFilters, EventFilters } from '@/types/filters'

const api = axios.create({ baseURL: import.meta.env.VITE_API_BASE_URL || '/api/v1' })

const get = async <T>(url: string, params?: Record<string, any>) => {
  const { data } = await api.get<T>(url, { params })
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

export default api
