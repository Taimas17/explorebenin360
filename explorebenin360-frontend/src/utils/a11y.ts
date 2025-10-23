export type EntityType = 'destination' | 'hebergement' | 'article' | 'guide' | 'event'

export function buildAlt(entityType: EntityType, name?: string, cityOrContext?: string) {
  const label = (s?: string) => (s || '').trim()
  const namePart = label(name)
  const ctx = label(cityOrContext)
  const suffix = ctx ? `, ${ctx}` : ''
  switch (entityType) {
    case 'destination':
      return namePart ? `Destination — ${namePart}${suffix}` : 'Destination'
    case 'hebergement':
      return namePart ? `Hébergement — ${namePart}${suffix}` : 'Hébergement'
    case 'article':
      return namePart ? `Article — ${namePart}${suffix ? ` — ${ctx}` : ''}` : 'Article'
    case 'guide':
      return namePart ? `Guide — ${namePart}${suffix}` : 'Guide'
    case 'event':
      return namePart ? `Événement — ${namePart}${suffix}` : 'Événement'
    default:
      return namePart || ''
  }
}

export function isDecorative(opts: { purpose?: 'background' | 'content' | 'icon'; hasVisibleText?: boolean; labelledBy?: string }) {
  if (opts.purpose === 'background' || opts.purpose === 'icon') return true
  if (opts.hasVisibleText && !opts.labelledBy) return true
  return false
}
