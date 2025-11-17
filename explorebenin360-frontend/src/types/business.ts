export type BookingStatus = 'pending'|'authorized'|'confirmed'|'cancelled'|'refunded'

export type Booking = {
  id: number
  offering: { id: number; title: string; slug?: string }
  user?: { id: number; name: string; email?: string } | null
  start_date: string
  end_date?: string | null
  guests: number
  status: BookingStatus
  currency: string
  amount: number
  commission_amount?: number
  receipt_url?: string | null
  created_at?: string
}

export type OfferingStatus = 'draft'|'published'

export type Offering = {
  id: number
  title: string
  slug: string
  description?: string
  price: number
  currency: string
  capacity?: number
  cover_image_url?: string | null
  gallery?: { src: string; alt: string; width?: number; height?: number }[]
  status: OfferingStatus
  availability_json?: Record<string, any> | null
}

export type ProviderUser = {
  id: number
  name: string
  company?: string | null
  email: string
  phone?: string | null
  status: 'pending'|'approved'|'rejected'
  kyc_submitted?: boolean
  kyc_verified?: boolean
  created_at?: string
}

export type Payout = {
  id: number
  date: string
  amount: number
  currency: string
  status: 'pending'|'paid'|'failed'
  reference: string
}

export type Thread = {
  id: number
  subject: string
  unread_count: number
  last_message_preview: string
  updated_at: string
}

export type Message = {
  id: number
  thread_id: number
  author: { id: number; name: string }
  body: string
  created_at: string
}

export type Review = {
  id: number
  booking_id: number
  rating: number
  comment: string
  photos: string[]
  user: {
    id: number
    name: string
  }
  offering?: {
    id: number
    title: string
    slug?: string
  }
  status: 'pending' | 'approved' | 'rejected'
  created_at: string
}
