import api from '@/lib/api'
import type { User } from '@/types/business'

export async function fetchProfile(): Promise<User> {
  const res = await api.get('/profile')
  return (res as any).data?.data || (res as any).data?.user || (res as any).user
}

export async function updateProfile(data: Partial<User>): Promise<User> {
  const res = await api.patch('/profile', data)
  return (res as any).data?.data || (res as any).data
}

export async function updatePassword(data: {
  current_password: string
  password: string
  password_confirmation: string
}): Promise<void> {
  await api.patch('/profile/password', data)
}

export async function uploadAvatar(file: File): Promise<string> {
  const formData = new FormData()
  formData.append('file', file)
  const res = await api.post('/profile/avatar', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  return (res as any).data?.data?.avatar_url || (res as any).data?.avatar_url
}

export async function deleteAvatar(): Promise<void> {
  await api.delete('/profile/avatar')
}

export async function uploadCover(file: File): Promise<string> {
  const formData = new FormData()
  formData.append('file', file)
  const res = await api.post('/profile/cover', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  return (res as any).data?.data?.cover_image_url || (res as any).data?.cover_image_url
}

export async function deleteCover(): Promise<void> {
  await api.delete('/profile/cover')
}

export async function deleteAccount(password: string): Promise<void> {
  await api.delete('/profile', { data: { password } })
}
