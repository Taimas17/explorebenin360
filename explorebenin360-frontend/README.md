# ExploreBenin360 Frontend – Media Components

Reusable media components with responsive images, galleries, videos, and 360° panoramas. Brand tokens: primary #FF6B35, secondary #00796B, accent #FFD166. Typography: Poppins/Inter.

## Components
- EBImage — responsive image with srcset, sizes, lazy-loading.
- EBGallery — grid + accessible lightbox with captions and keyboard nav.
- EB360Viewer — wrapper around Pannellum loaded dynamically from CDN.
- EBVideo — HTML5 video with poster and controls.

See `src/views` for sample usage on Home, Destinations, Hébergements, Guides, Blog.

## Env
Create a `.env` and set:
```
VITE_MEDIA_PROVIDER=cloudinary|s3
VITE_CLOUDINARY_CLOUD_NAME=
VITE_CLOUDINARY_BASE_URL=
VITE_MEDIA_MAX_WIDTH=1600
```

## Usage example
```vue
<EBImage :src="'my-folder/my-public-id'" alt="Lac Nokoué" />
```
When provider is Cloudinary, `src` can be a public_id or a full Cloudinary URL and will be transformed to optimized formats (q=auto, f=auto). For S3, the URL is passed through.
