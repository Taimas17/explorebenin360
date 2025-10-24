import { useHead } from '@vueuse/head'

const SITE = import.meta.env.VITE_SITE_URL || window.location.origin

export type MetaInput = {
  title: string
  description?: string
  path?: string
  image?: string
}

export function setPageMeta({ title, description, path, image }: MetaInput) {
  const url = path ? new URL(path, SITE).toString() : SITE
  const links: any[] = [{ rel: 'canonical', href: url }]
  const loc = (new URL(url)).toString()
  const base = loc.replace(/[?#].*$/, '')
  links.push({ rel: 'alternate', hreflang: 'fr', href: base + (base.includes('?') ? '&' : '?') + 'lang=fr' })
  links.push({ rel: 'alternate', hreflang: 'en', href: base + (base.includes('?') ? '&' : '?') + 'lang=en' })

  useHead({
    title,
    meta: [
      description ? { name: 'description', content: description } : {},
      { property: 'og:title', content: title },
      description ? { property: 'og:description', content: description } : {},
      { property: 'og:type', content: 'website' },
      { property: 'og:url', content: url },
      image ? { property: 'og:image', content: image } : {},
      { name: 'twitter:card', content: 'summary_large_image' },
      { name: 'twitter:title', content: title },
      description ? { name: 'twitter:description', content: description } : {},
      image ? { name: 'twitter:image', content: image } : {},
    ].filter(Boolean) as any,
    link: links,
  })
}
