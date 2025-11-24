import api from '@/lib/api'
import type { User } from '@/types/business'
import type { Paginated } from '@/types/pagination'

export async function fetchAdminUsers(filters?: {
  q?: string
  role?: string
  account_status?: string
  provider_status?: string
  sort?: string
  page?: number
  per_page?: number
}): Promise<Paginated<User>> {
  const res = await api.get('/admin/users', { params: filters })
  return res.data
}

export async function fetchUserById(id: number): Promise<User> {
  const res = await api.get(`/admin/users/${id}`)
  return res.data.data
}

export async function updateUser(id: number, data: Partial<User>): Promise<User> {
  const res = await api.patch(`/admin/users/${id}`, data)
  return res.data.data
}

export async function deleteUser(id: number): Promise<void> {
  await api.delete(`/admin/users/${id}`)
}

export async function suspendUser(id: number, reason: string, duration_days?: number): Promise<void> {
  await api.post(`/admin/users/${id}/suspend`, { reason, duration_days })
}

export async function unsuspendUser(id: number): Promise<void> {
  await api.post(`/admin/users/${id}/unsuspend`)
}

export async function banUser(id: number, reason: string): Promise<void> {
  await api.post(`/admin/users/${id}/ban`, { reason })
}

export async function updateUserRoles(id: number, roles: string[]): Promise<string[]> {
  const res = await api.patch(`/admin/users/${id}/roles`, { roles })
  return res.data.data.roles
}

export async function resetUserPassword(id: number): Promise<void> {
  await api.post(`/admin/users/${id}/reset-password`)
}
