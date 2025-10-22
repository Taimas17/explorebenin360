export type Provider = 'cloudinary' | 's3' | 'local';

interface CloudinaryOptions {
  width?: number;
  quality?: number | 'auto';
  format?: 'auto' | 'jpg' | 'png' | 'webp' | 'avif';
  dpr?: number | 'auto';
}

const WIDTHS = [400, 800, 1200, 1600] as const;
export const MEDIA_WIDTHS = WIDTHS as unknown as number[];

export function buildCloudinaryUrl(publicIdOrUrl: string, opts: CloudinaryOptions = {}) {
  const cloudName = import.meta.env.VITE_CLOUDINARY_CLOUD_NAME;
  if (!cloudName) return publicIdOrUrl;
  if (publicIdOrUrl.startsWith('http')) {
    return applyCloudinaryTransform(publicIdOrUrl, opts);
  }
  const base = `https://res.cloudinary.com/${cloudName}/image/upload/`;
  const t: string[] = [];
  if (opts.width) t.push(`w_${opts.width}`);
  if (opts.quality !== undefined) t.push(`q_${opts.quality}`);
  if (opts.format) t.push(`f_${opts.format}`);
  if (opts.dpr) t.push(`dpr_${opts.dpr}`);
  const transform = t.length ? t.join(',') + '/' : '';
  return `${base}${transform}${publicIdOrUrl}`;
}

function applyCloudinaryTransform(url: string, opts: CloudinaryOptions) {
  const parts = url.split('/upload/');
  if (parts.length < 2) return url;
  const t: string[] = [];
  if (opts.width) t.push(`w_${opts.width}`);
  if (opts.quality !== undefined) t.push(`q_${opts.quality}`);
  if (opts.format) t.push(`f_${opts.format}`);
  if (opts.dpr) t.push(`dpr_${opts.dpr}`);
  const transform = t.length ? t.join(',') + '/' : '';
  return `${parts[0]}/upload/${transform}${parts[1]}`;
}

export function buildWidthSrcset(src: string, provider: Provider = 'local') {
  if (provider === 'cloudinary') {
    return MEDIA_WIDTHS.map((w) => `${buildCloudinaryUrl(src, { width: w, format: 'auto', quality: 'auto' })} ${w}w`).join(', ');
  }
  return undefined as unknown as string | undefined;
}

export function buildDprSrcset(src: string, width: number, provider: Provider = 'local') {
  const dprs = [1, 2];
  if (provider === 'cloudinary') {
    return dprs.map((dpr) => `${buildCloudinaryUrl(src, { width, dpr, format: 'auto', quality: 'auto' })} ${dpr}x`).join(', ');
  }
  return undefined as unknown as string | undefined;
}

export function buildCloudinaryLqip(src: string) {
  return buildCloudinaryUrl(src, { width: 24, quality: 30, format: 'auto' });
}

export function passthroughUrl(url: string) {
  return url;
}

export type GalleryItem = { src: string; alt: string; caption?: string; width?: number; height?: number };

export function mapToGalleryItems(source: any, opts: { title?: string; fallbackUrl?: string } = {}): GalleryItem[] {
  const title = opts.title || source?.title || 'Image';
  const imagesRaw = (source?.images || source?.gallery || source?.media) as any[] | undefined;
  const items: GalleryItem[] = [];

  const pushItem = (src?: string, alt?: string, width?: number | null, height?: number | null, caption?: string) => {
    if (!src) return;
    items.push({
      src,
      alt: alt || `${title} — Photo ${items.length + 1}`,
      caption: caption || undefined,
      width: width ?? undefined,
      height: height ?? undefined,
    });
  };

  if (Array.isArray(imagesRaw) && imagesRaw.length) {
    imagesRaw.forEach((it: any, idx: number) => {
      if (!it) return;
      if (typeof it === 'string') {
        pushItem(it);
      } else if (typeof it === 'object') {
        const src = it.url || it.src || it.path || it.public_id || it.publicId;
        const alt = it.alt || it.title || it.caption;
        const width = it.width ?? it.metadata?.width ?? it.meta?.width ?? null;
        const height = it.height ?? it.metadata?.height ?? it.meta?.height ?? null;
        const caption = it.caption;
        pushItem(src, alt, width, height, caption);
      }
    });
  } else if (source?.cover_image_url) {
    pushItem(source.cover_image_url, `${title}`);
  } else if (opts.fallbackUrl) {
    pushItem(opts.fallbackUrl, `${title}`);
  }

  return items;
}
