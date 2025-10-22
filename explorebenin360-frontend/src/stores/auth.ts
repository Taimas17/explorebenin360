import { defineStore } from 'pinia'
import { apiLogin, apiRegister, apiLogout, apiMe, setAuthToken } from '@/lib/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({ user: null as any, token: (typeof window !== 'undefined' ? localStorage.getItem('eb360_token') : null) as string | null, loading: false }),
  getters: { isAuthenticated: (s) => !!s.token },
  actions: {
    init() {
      if (this.token) setAuthToken(this.token)
      if (this.token) this.fetchMe()
    },
    async register(payload: { name: string; email: string; password: string }) {
      this.loading = true
      try {
        const res: any = await apiRegister(payload)
        this.token = res.token
        localStorage.setItem('eb360_token', this.token!)
        setAuthToken(this.token)
        this.user = res.user
      } finally { this.loading = false }
    },
    async login(payload: { email: string; password: string }) {
      this.loading = true
      try {
        const res: any = await apiLogin(payload)
        this.token = res.token
        localStorage.setItem('eb360_token', this.token!)
        setAuthToken(this.token)
        this.user = res.user
      } finally { this.loading = false }
    },
    async logout() {
      try { await apiLogout() } catch {}
      this.token = null
      this.user = null
      localStorage.removeItem('eb360_token')
      setAuthToken(null)
    },
    async fetchMe() {
      try {
        const res: any = await apiMe()
        this.user = res.user
      } catch (e) {
        this.logout()
      }
    },
  }
})
