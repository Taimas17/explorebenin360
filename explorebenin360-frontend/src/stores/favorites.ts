import { defineStore } from 'pinia'
import { reactive } from 'vue'
import { useAuthStore } from './auth'
import { getFavorites, addFavorite, removeFavorite } from '@/lib/api'

export type FavType = 'destination' | 'hebergement' | 'article' | 'guide' | 'offering'

const LOCAL_KEY = 'eb360:favorites'

const emptySets = () => ({
  destination: new Set<number>(),
  hebergement: new Set<number>(),
  article: new Set<number>(),
  guide: new Set<number>(),
  offering: new Set<number>(),
}) as Record<FavType, Set<number>>

export const useFavoritesStore = defineStore('favorites', {
  state: () => ({
    items: emptySets(),
    entities: reactive({ destination: {} as Record<number, any>, hebergement: {}, article: {}, guide: {}, offering: {} }) as Record<FavType, Record<number, any>>,
    usingServer: true,
    loaded: false,
  }),
  actions: {
    init() {
      this.load()
      const auth = useAuthStore()
      if (auth.isAuthenticated) this.syncOnLogin().catch(() => {})
    },
    load() {
      try {
        const raw = typeof window !== 'undefined' ? localStorage.getItem(LOCAL_KEY) : null
        if (raw) {
          const parsed = JSON.parse(raw) as { [K in FavType]?: number[] }
          const next = emptySets()
          ;(['destination','hebergement','article','guide','offering'] as FavType[]).forEach((t) => {
            const arr = parsed[t] || []
            next[t] = new Set(arr)
          })
          this.items = next
        }
      } catch {}
      this.loaded = true
    },
    save() {
      try {
        const obj: Record<string, number[]> = {}
        ;(['destination','hebergement','article','guide','offering'] as FavType[]).forEach((t) => {
          obj[t] = Array.from(this.items[t])
        })
        localStorage.setItem(LOCAL_KEY, JSON.stringify(obj))
      } catch {}
    },
    isFav(type: FavType, id: number) {
      return this.items[type]?.has(id) || false
    },
    remember(type: FavType, id: number, entity: any) {
      if (entity) this.entities[type][id] = entity
    },
    async toggle(type: FavType, id: number, entity?: any) {
      const wasFav = this.isFav(type, id)
      if (wasFav) this.items[type].delete(id)
      else this.items[type].add(id)
      if (entity) this.remember(type, id, entity)
      this.save()

      const auth = useAuthStore()
      if (!auth.isAuthenticated || !this.usingServer) return

      try {
        if (wasFav) await removeFavorite(type, id)
        else await addFavorite(type, id)
      } catch (e: any) {
        if (e?.response?.status === 404) {
          this.usingServer = false
          return
        }
        // On other server errors, keep optimistic state and fallback silently
      }
    },
    async syncOnLogin() {
      const auth = useAuthStore()
      if (!auth.isAuthenticated) return
      try {
        const res: any = await getFavorites()
        const server: Record<FavType, number[]> = res?.data || { destination: [], hebergement: [], article: [], guide: [], offering: [] }
        const merged = emptySets()
        ;(['destination','hebergement','article','guide','offering'] as FavType[]).forEach((t) => {
          const localArr = Array.from(this.items[t])
          const union = new Set<number>([...server[t] || [], ...localArr])
          merged[t] = union
        })
        this.items = merged
        this.save()
        // Push missing local-only to server
        for (const t of ['destination','hebergement','article','guide','offering'] as FavType[]) {
          const s = new Set(server[t] || [])
          for (const id of this.items[t]) {
            if (!s.has(id)) {
              try { await addFavorite(t, id) } catch {}
            }
          }
        }
        this.usingServer = true
      } catch (e: any) {
        if (e?.response?.status === 404) {
          this.usingServer = false
          return
        }
        // Other errors: keep local only
        this.usingServer = false
      }
    },
    onLogout() {
      // Keep local favorites; nothing to do besides ensuring local is saved
      this.save()
    }
  }
})
