interface CloudinaryOptions {
  width?: number;
  quality?: number | 'auto';
  format?: 'auto' | 'jpg' | 'png' | 'webp' | 'avif';
  dpr?: number | 'auto';
}

export function buildCloudinaryUrl(publicIdOrUrl: string, opts: CloudinaryOptions = {}) {
  const cloudName = import.meta.env.VITE_CLOUDINARY_CLOUD_NAME;
  let publicId = publicIdOrUrl;
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
  return `${base}${transform}${publicId}`;
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

export function passthroughUrl(url: string) {
  return url;
}
