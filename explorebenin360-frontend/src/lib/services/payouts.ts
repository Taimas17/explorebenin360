import api from '@/lib/api'
import type { PaymentMethod, Payout, Balance } from '@/types/business'

export async function fetchPaymentMethods(): Promise<PaymentMethod[]> {
  const res = await api.get('/provider/payment-methods')
  return res.data.data || []
}

export async function createPaymentMethod(payload: {
  type: 'bank_account' | 'mobile_money' | 'paypal'
  account_name: string
  account_number: string
  bank_name?: string
  bank_code?: string
  mobile_provider?: string
  country?: string
  is_default?: boolean
}): Promise<PaymentMethod> {
  const res = await api.post('/provider/payment-methods', payload)
  return res.data.data
}

export async function setDefaultPaymentMethod(id: number): Promise<void> {
  await api.patch(`/provider/payment-methods/${id}/set-default`)
}

export async function deletePaymentMethod(id: number): Promise<void> {
  await api.delete(`/provider/payment-methods/${id}`)
}

export async function fetchBalance(): Promise<Balance> {
  const res = await api.get('/provider/balance')
  return res.data.data
}

export async function fetchPayouts(params?: {
  status?: 'pending' | 'processing' | 'completed' | 'failed' | 'cancelled'
  per_page?: number
}): Promise<{ data: Payout[], meta: any }> {
  const res = await api.get('/provider/payouts', { params })
  return { data: res.data.data, meta: res.data.meta }
}

export async function fetchPayout(id: number): Promise<Payout> {
  const res = await api.get(`/provider/payouts/${id}`)
  return res.data.data
}

export async function createPayout(payload: {
  amount: number
  payment_method_id?: number
}): Promise<Payout> {
  const res = await api.post('/provider/payouts', payload)
  return res.data.data
}

export async function cancelPayout(id: number): Promise<void> {
  await api.patch(`/provider/payouts/${id}/cancel`)
}

export async function fetchAdminPayouts(params?: {
  status?: 'pending' | 'processing' | 'completed' | 'failed' | 'cancelled'
  per_page?: number
}): Promise<{ data: Payout[], meta: any }> {
  const res = await api.get('/admin/payouts', { params })
  return { data: res.data.data, meta: res.data.meta }
}

export async function processPayout(id: number, payload?: {
  transaction_ref?: string
  admin_notes?: string
}): Promise<void> {
  await api.patch(`/admin/payouts/${id}/process`, payload)
}

export async function completePayout(id: number, payload?: {
  transaction_ref?: string
  admin_notes?: string
}): Promise<void> {
  await api.patch(`/admin/payouts/${id}/complete`, payload)
}

export async function failPayout(id: number, payload: {
  failure_reason: string
  admin_notes?: string
}): Promise<void> {
  await api.patch(`/admin/payouts/${id}/fail`, payload)
}
