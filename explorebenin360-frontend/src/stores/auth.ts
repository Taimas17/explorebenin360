import { defineStore } from 'pinia'
import { apiLogin, apiRegister, apiLogout, apiMe } from '@/lib/api'
import { updateProfile as apiUpdateProfile } from '@/lib/services/profile'
import { uploadAvatar as apiUploadAvatar } from '@/lib/services/profile'
import { useFavoritesStore } from './favorites'

export const useAuthStore = defineStore('auth', {
  state: () => ({ 
    user: null as any,
    loading: false 
  }),
  getters: {
    isAuthenticated: (s) => !!s.user,
    roles: (s) => Array.isArray(s.user?.roles) ? s.user.roles.map((r: any) => (typeof r === 'string' ? r : r.name)) : [],
    hasRole() { return (role: string) => (this.roles as string[]).includes(role) },
  },
  actions: {
    init() {
      this.fetchMe()
    },
    async register(payload: { name: string; email: string; password: string }) {
      this.loading = true
      try {
        const res: any = await apiRegister(payload)
        this.user = res.user
        await this.fetchMe()
        try { await useFavoritesStore().syncOnLogin() } catch {}
      } finally { this.loading = false }
    },
    async login(payload: { email: string; password: string }) {
      this.loading = true
      try {
        const res: any = await apiLogin(payload)
        this.user = res.user
        await this.fetchMe()
        try { await useFavoritesStore().syncOnLogin() } catch {}
      } finally { this.loading = false }
    },
    async logout() {
      try { await apiLogout() } catch {}
      this.user = null
      try { useFavoritesStore().onLogout() } catch {}
    },
    async fetchMe() {
      try {
        const res: any = await apiMe()
        this.user = res.user
      } catch (e) {
        this.logout()
      }
    },
    async updateProfile(data: any) {
      const updated = await apiUpdateProfile(data)
      this.user = { ...(this.user || {}), ...updated }
      return updated
    },
    async uploadAvatar(file: File) {
      const url = await apiUploadAvatar(file)
      if (this.user) this.user.avatar_url = url
      return url
    }
  }
})
