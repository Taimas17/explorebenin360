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

export type PaymentMethodType = 'bank_account' | 'mobile_money' | 'paypal'

export type PaymentMethod = {
  id: number
  type: PaymentMethodType
  account_name: string
  account_number_masked: string
  bank_name?: string | null
  mobile_provider?: string | null
  country: string
  is_default: boolean
  is_verified: boolean
  verified_at?: string | null
  created_at: string
}

export type PayoutStatus = 'pending' | 'processing' | 'completed' | 'failed' | 'cancelled'

export type Payout = {
  id: number
  provider_id: number
  payment_method_id: number
  payment_method?: PaymentMethod
  amount: number
  currency: string
  status: PayoutStatus
  reference: string
  transaction_ref?: string | null
  admin_notes?: string | null
  failure_reason?: string | null
  requested_at: string
  processed_at?: string | null
  completed_at?: string | null
  processed_by?: { id: number; name: string } | null
  created_at: string
}

export type Balance = {
  total_earnings: number
  total_payouts: number
  pending_payouts: number
  available_balance: number
  currency: string
}

export type MessageThread = {
  id: number
  subject: string
  status: 'open' | 'closed'
  traveler: { id: number; name: string }
  provider: { id: number; name: string; business_name?: string }
  booking?: { id: number; offering_title: string; start_date: string } | null
  offering?: { id: number; title: string; slug: string } | null
  unread_count: number
  last_message_preview: string
  last_message_at: string
  created_at: string
}

export type Message = {
  id: number
  thread_id: number
  sender: { id: number; name: string }
  body: string
  read_at: string | null
  created_at: string
}

export type User = {
  id: number
  name: string
  email: string
  phone?: string | null
  roles?: Array<{ name: string }>|string[]
  provider_status?: 'none'|'pending'|'approved'|'rejected'|'suspended'
  business_name?: string | null
  bio?: string | null

  // Profile fields
  avatar_url?: string | null
  cover_image_url?: string | null
  date_of_birth?: string | null
  age?: number | null
  gender?: 'male' | 'female' | 'other' | 'prefer_not_to_say' | null
  country?: string | null
  city?: string | null
  full_location?: string
  address?: string | null
  postal_code?: string | null
  website_url?: string | null
  social_links?: {
    facebook?: string
    instagram?: string
    twitter?: string
    linkedin?: string
  } | null
  preferences?: {
    language?: 'fr' | 'en'
    currency?: 'XOF' | 'EUR' | 'USD'
    notifications_enabled?: boolean
  } | null
  about_me?: string | null

  // Admin/account management
  account_status?: 'active' | 'suspended' | 'banned'
  email_verified_at?: string | null
  created_at?: string
  last_login_at?: string | null
  login_count?: number
  suspended_at?: string | null
  suspension_reason?: string | null
  suspended_by?: { id: number; name: string } | null

  // Counts
  bookings_count?: number
  favorites_count?: number
  offerings_count?: number
}
