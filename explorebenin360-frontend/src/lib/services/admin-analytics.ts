import api from '@/lib/api'

export type AnalyticsOverview = {
  period: string
  start_date: string | null
  end_date: string
  kpis: {
    users: {
      total: number
      new: number
      active: number
      growth: number
    }
    providers: {
      total: number
      pending: number
      approved: number
      active: number
    }
    content: {
      accommodations: number
      accommodations_published: number
      articles: number
      articles_published: number
      events: number
      events_published: number
      guides: number
      guides_published: number
      places: number
      places_published: number
    }
    bookings: {
      total: number
      confirmed: number
      pending: number
      cancelled: number
      conversion_rate: number
    }
    revenue: {
      total: number
      commission: number
      average: number
      currency: string
    }
    engagement: {
      favorites: number
      reviews: number
      average_rating: number
    }
  }
}

export type TimeseriesData = {
  metric: string
  period: string
  granularity: string
  series: Array<{
    date: string
    value: number
  }>
}

export type TopContentData = {
  type: string
  metric: string
  period: string
  items: Array<{
    id: number
    title: string
    slug: string
    cover_image_url?: string
    metric_value: number
  }>
}

export type ActivityLog = {
  id: number
  type: string
  description: string
  user?: { name: string }
  created_at: string
}

export async function fetchAnalyticsOverview(params?: {
  period?: string
  compare_previous?: boolean
}): Promise<AnalyticsOverview> {
  const res = await api.get('/admin/analytics/overview', { params })
  return res.data.data
}

export async function fetchTimeseries(params: {
  metric: 'users' | 'bookings' | 'revenue' | 'favorites'
  period?: string
  granularity?: string
}): Promise<TimeseriesData> {
  const res = await api.get('/admin/analytics/timeseries', { params })
  return res.data.data
}

export async function fetchTopContent(params: {
  type: 'accommodations' | 'guides' | 'offerings' | 'articles'
  metric?: string
  limit?: number
  period?: string
}): Promise<TopContentData> {
  const res = await api.get('/admin/analytics/top-content', { params })
  return res.data.data
}

export async function fetchRecentActivity(limit?: number): Promise<ActivityLog[]> {
  const res = await api.get('/admin/analytics/recent-activity', { params: { limit } })
  return res.data.data
}

export async function exportData(params: {
  type: 'users' | 'bookings' | 'revenue' | 'providers'
  format?: 'csv' | 'json'
  period?: string
}): Promise<Blob> {
  const res = await api.get('/admin/analytics/export', {
    params,
    responseType: 'blob'
  })
  return res.data
}

// Helper pour télécharger le fichier
export function downloadFile(blob: Blob, filename: string) {
  const url = window.URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = filename
  link.click()
  window.URL.revokeObjectURL(url)
}
