/**
 * Liste blanche des domaines autorisés pour les redirections
 */
const ALLOWED_DOMAINS = [
  'explorebenin360.com',
  'www.explorebenin360.com',
  'localhost',
]

/**
 * Valider qu'une URL est sûre pour redirection
 */
export function isSafeRedirectUrl(url: string | undefined | null): boolean {
  if (!url) return false

  try {
    // URLs relatives sont OK
    if (url.startsWith('/')) {
      // Mais pas les protocol-relative URLs (//evil.com)
      if (url.startsWith('//')) return false
      return true
    }

    // Parser l'URL pour valider
    const parsed = new URL(url, window.location.origin)

    // Vérifier le protocol (seulement http/https)
    if (parsed.protocol !== 'http:' && parsed.protocol !== 'https:') {
      return false
    }

    // En développement, autoriser localhost
    if (import.meta && (import.meta as any).env && (import.meta as any).env.DEV && parsed.hostname === 'localhost') {
      return true
    }

    // Vérifier que le domaine est dans la whitelist
    const hostname = parsed.hostname.toLowerCase()
    return ALLOWED_DOMAINS.some(domain =>
      hostname === domain || hostname.endsWith('.' + domain)
    )
  } catch {
    return false
  }
}

/**
 * Rediriger de manière sécurisée
 */
export function safeRedirect(url: string | undefined | null, fallback: string = '/'): string {
  if (!url || !isSafeRedirectUrl(url)) {
    return fallback
  }
  return url
}

/**
 * Valider une URL externe (pour liens)
 */
export function isSafeExternalUrl(url: string | undefined | null): boolean {
  if (!url) return false

  try {
    const parsed = new URL(url)

    // Seulement http/https
    if (parsed.protocol !== 'http:' && parsed.protocol !== 'https:') {
      return false
    }

    return true
  } catch {
    return false
  }
}
