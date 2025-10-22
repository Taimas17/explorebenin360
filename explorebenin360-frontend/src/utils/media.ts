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

export function buildWidthSrcset(src: string, provider: 'cloudinary' | 's3' = 'cloudinary') {
  if (provider === 'cloudinary') {
    return MEDIA_WIDTHS.map((w) => `${buildCloudinaryUrl(src, { width: w, format: 'auto', quality: 'auto' })} ${w}w`).join(', ');
  }
  return MEDIA_WIDTHS.map((w) => `${src} ${w}w`).join(', ');
}

export function buildDprSrcset(src: string, width: number, provider: 'cloudinary' | 's3' = 'cloudinary') {
  const dprs = [1, 2];
  if (provider === 'cloudinary') {
    return dprs.map((dpr) => `${buildCloudinaryUrl(src, { width, dpr, format: 'auto', quality: 'auto' })} ${dpr}x`).join(', ');
  }
  return dprs.map((dpr) => `${src} ${dpr}x`).join(', ');
}

export function buildCloudinaryLqip(src: string) {
  return buildCloudinaryUrl(src, { width: 24, quality: 30, format: 'auto' });
}

export function passthroughUrl(url: string) {
  return url;
}
