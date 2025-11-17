import { getFavorites as apiFetchFavorites, fetchPlaceById, fetchAccommodationById, fetchArticleById, fetchGuideById } from '@/lib/api'
import { useFavoritesStore, type FavType } from '@/stores/favorites'
import type { Place, Accommodation, Article, Guide } from '@/types/content'

export type Favorites = {
  places: Place[]
  accommodations: Accommodation[]
  articles: Article[]
  guides: Guide[]
}

export async function fetchFavorites(): Promise<Favorites> {
  const favStore = useFavoritesStore()

  if (!favStore.loaded) {
    favStore.init()
  }

  let idsByType: Record<FavType, number[]>

  try {
    const response = await apiFetchFavorites()
    const serverIds = response?.data || {}
    idsByType = buildMergedIds(favStore, serverIds)
    applyIdsToStore(favStore, idsByType)
  } catch (error) {
    console.error('Failed to fetch favorites:', error)
    return collectFromStore(favStore)
  }

  const [places, accommodations, articles, guides] = await Promise.all([
    fetchEntitiesByIds<Place>(idsByType.destination, favStore, 'destination', fetchPlaceById),
    fetchEntitiesByIds<Accommodation>(idsByType.hebergement, favStore, 'hebergement', fetchAccommodationById),
    fetchEntitiesByIds<Article>(idsByType.article, favStore, 'article', fetchArticleById),
    fetchEntitiesByIds<Guide>(idsByType.guide, favStore, 'guide', fetchGuideById),
  ])

  return { places, accommodations, articles, guides }
}

function buildMergedIds(
  favStore: ReturnType<typeof useFavoritesStore>,
  serverIds: Partial<Record<FavType, number[]>>
): Record<FavType, number[]> {
  const types: FavType[] = ['destination', 'hebergement', 'article', 'guide']
  const merged: Record<FavType, number[]> = {
    destination: [],
    hebergement: [],
    article: [],
    guide: [],
  }

  for (const type of types) {
    const union = new Set<number>(serverIds[type] || [])
    for (const id of favStore.items[type]) union.add(id)
    merged[type] = Array.from(union)
  }

  return merged
}

function applyIdsToStore(
  favStore: ReturnType<typeof useFavoritesStore>,
  idsByType: Record<FavType, number[]>
) {
  const next = {
    destination: new Set(idsByType.destination),
    hebergement: new Set(idsByType.hebergement),
    article: new Set(idsByType.article),
    guide: new Set(idsByType.guide),
  } as Record<FavType, Set<number>>

  favStore.items = next
  favStore.save()
}

async function fetchEntitiesByIds<T>(
  ids: number[],
  favStore: ReturnType<typeof useFavoritesStore>,
  type: FavType,
  fetchFn: (id: number) => Promise<{ data: T }>
): Promise<T[]> {
  if (!ids.length) return []

  const uniqueIds = Array.from(new Set(ids))
  const cache = favStore.entities[type] || {}
  const existing = new Map<number, T>()
  const missing: number[] = []

  for (const id of uniqueIds) {
    const cached = cache[id]
    if (cached) existing.set(id, cached as T)
    else missing.push(id)
  }

  if (missing.length) {
    const results = await Promise.allSettled(missing.map((id) => fetchFn(id)))
    results.forEach((result, index) => {
      const id = missing[index]
      if (result.status === 'fulfilled') {
        const entity = result.value?.data
        if (entity) {
          favStore.remember(type, id, entity)
          existing.set(id, entity)
        }
      } else {
        const cached = cache[id]
        if (cached) existing.set(id, cached as T)
      }
    })
  }

  return uniqueIds
    .map((id) => {
      const entity = existing.get(id) || (cache[id] as T | undefined)
      return entity || null
    })
    .filter((entity): entity is T => entity !== null)
}

function collectFromStore(
  favStore: ReturnType<typeof useFavoritesStore>
): Favorites {
  return {
    places: collectType<Place>(favStore, 'destination'),
    accommodations: collectType<Accommodation>(favStore, 'hebergement'),
    articles: collectType<Article>(favStore, 'article'),
    guides: collectType<Guide>(favStore, 'guide'),
  }
}

function collectType<T>(
  favStore: ReturnType<typeof useFavoritesStore>,
  type: FavType
): T[] {
  return Array.from(favStore.items[type])
    .map((id) => favStore.entities[type][id] as T | undefined)
    .filter((entity): entity is T => !!entity)
}
