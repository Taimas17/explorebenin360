import { 
  fetchProviderOfferings as apiFetchProviderOfferings,
  createProviderOffering as apiCreateProviderOffering,
  updateProviderOffering as apiUpdateProviderOffering,
  fetchProviderOffering as apiFetchProviderOffering,
  deleteProviderOffering as apiDeleteProviderOffering
} from '@/lib/api'

export type Offering = {
  id: number
  title: string
  slug: string
  type: 'accommodation' | 'experience' | 'guide_service'
  description?: string
  price: number
  currency: string
  capacity: number
  status: 'draft' | 'published' | 'archived'
  cover_image_url?: string
  gallery?: string[]
  availability?: any
  cancellation_policy?: string
  bookings_count?: number
  created_at: string
  updated_at: string
}

export async function providerOfferings(status?: string): Promise<Offering[]> {
  try {
    const response = await apiFetchProviderOfferings(status ? { status } : undefined)
    return response.data
  } catch (error) {
    console.error('Failed to fetch provider offerings:', error)
    return []
  }
}

export async function fetchOfferingById(id: number): Promise<Offering | null> {
  try {
    const response = await apiFetchProviderOffering(id)
    return response.data
  } catch (error) {
    console.error('Failed to fetch offering:', error)
    return null
  }
}

export async function createOffering(payload: Partial<Offering>): Promise<Offering> {
  const response = await apiCreateProviderOffering(payload)
  return response.data
}

export async function updateOffering(id: number, payload: Partial<Offering>): Promise<Offering> {
  const response = await apiUpdateProviderOffering(id, payload)
  return response.data
}

export async function deleteOffering(id: number): Promise<void> {
  await apiDeleteProviderOffering(id)
}
