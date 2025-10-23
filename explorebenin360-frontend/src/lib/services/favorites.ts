import type { Place, Accommodation, Article } from '@/types/content'

const useStubs = (import.meta.env.VITE_USE_STUBS === 'true')
const delay = (ms = 200) => new Promise((r) => setTimeout(r, ms))

export type Favorites = { places: Place[]; accommodations: Accommodation[]; articles: Article[] }

const stub: Favorites = {
  places: [
    { id: 1, title: 'Ganvié', slug: 'ganvie', type: 'culture', description: 'Village lacustre', city: 'Abomey-Calavi', country: 'Benin', lat: 6.455, lng: 2.336, tags: ['lac','pêche'], cover_image_url: '/src/assets/brand/images/thumbs/destination-thumb.png', rating_avg: 4.7, review_count: 58, featured: true, status: 'published' },
  ],
  accommodations: [
    { id: 10, title: 'Ecolodge Nokoué', slug: 'ecolodge-nokoue', type: 'ecolodge', description: 'Séjour écoresponsable', address: 'Lac Nokoué', city: 'Sô-Ava', lat: 6.5, lng: 2.4, price_per_night: 45000, currency: 'XOF', amenities: ['ponton','canoë'], capacity: 4, rating_avg: 4.5, review_count: 23, featured: false, status: 'published', cover_image_url: '/src/assets/brand/images/thumbs/hebergement-thumb.png' }
  ],
  articles: [
    { id: 100, title: '5 lieux à voir au Bénin', slug: '5-lieux-a-voir', excerpt: 'Notre sélection…', body: '', author_name: 'Rédaction', category: 'Inspiration', tags: ['voyage'], cover_image_url: '/src/assets/brand/images/thumbs/article-thumb.png', status: 'published', published_at: new Date().toISOString() }
  ]
}

export async function fetchFavorites(): Promise<Favorites> {
  if (useStubs) { await delay(); return stub }
  // TODO: Replace with real endpoints when available
  // Example: const res = await api.get('/dashboard/favorites')
  // return res.data
  await delay();
  return { places: [], accommodations: [], articles: [] }
}
