import fs from 'fs'
import path from 'path'
import axios from 'axios'

const base = process.env.VITE_API_BASE_URL || process.env.API_BASE_URL || 'http://localhost:8000/api/v1'
const site = process.env.SITE_URL || 'https://explorebenin360.com'

const routes = ['/', '/destinations', '/hebergements', '/guides', '/blog', '/agenda']

const fetchList = async (url: string) => {
  try { const { data } = await axios.get(url, { params: { per_page: 10, featured: true } }); return data.data || [] } catch { return [] }
}

const run = async () => {
  const places = await fetchList(`${base}/places`)
  const hebergements = await fetchList(`${base}/accommodations`)
  const articles = await fetchList(`${base}/articles`)
  const events = await fetchList(`${base}/events`)

  const urls = [
    ...routes,
    ...places.map((p: any) => `/destinations/${p.slug}`),
    ...hebergements.map((h: any) => `/hebergements/${h.slug}`),
    ...articles.map((a: any) => `/blog/${a.slug}`),
    ...events.map((e: any) => `/agenda/${e.slug}`),
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
