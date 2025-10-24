import fs from 'fs'
import path from 'path'
import axios from 'axios'

const base = process.env.VITE_API_BASE_URL || process.env.API_BASE_URL || 'http://localhost:8000/api/v1'
const site = process.env.SITE_URL || 'https://explorebenin360.com'

const routes = ['/', '/destinations', '/hebergements', '/guides', '/blog', '/agenda', '/offerings']

const fetchAll = async (url: string) => {
  const items: any[] = []
  let page = 1
  while (true) {
    try {
      const { data } = await axios.get(url, { params: { per_page: 50, page } })
      const arr = data.data || []
      items.push(...arr)
      const meta = data.meta || { total: arr.length, per_page: 50, current_page: page }
      if (!meta || arr.length < (meta.per_page || 50)) break
      page++
      if (page > 50) break
    } catch { break }
  }
  return items
}

const run = async () => {
  const places = await fetchAll(`${base}/places`)
  const hebergements = await fetchAll(`${base}/accommodations`)
  const articles = await fetchAll(`${base}/articles`)
  const events = await fetchAll(`${base}/events`)
  const offerings = await fetchAll(`${base}/offerings`)

  const urls = [
    ...routes,
    ...places.map((p: any) => `/destinations/${p.slug}`),
    ...hebergements.map((h: any) => `/hebergements/${h.slug}`),
    ...articles.map((a: any) => `/blog/${a.slug}`),
    ...events.map((e: any) => `/agenda/${e.slug}`),
    ...offerings.map((o: any) => `/offerings/${o.slug}`),
  ]

  const xml = `<?xml version="1.0" encoding="UTF-8"?>\n` +
`<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">` +
urls.map(u => `<url><loc>${site}${u}</loc></url>`).join('') +
`</urlset>`

  const out = path.resolve(process.cwd(), 'public', 'sitemap.xml')
  fs.writeFileSync(out, xml)
  console.log('sitemap.xml generated:', out)
}

run()
