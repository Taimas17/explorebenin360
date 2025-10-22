# ExploreBenin360 Backend – Media API & Storage

This backend exposes a pluggable media API for images, videos, 360° panoramas, and documents with CDN-backed storage (Cloudinary or S3).

## Endpoints (prefix /api/v1)
- POST /media — multipart upload. Accepts single field `file` or array `files[]`. Returns created media JSON.
- GET /media?model_type=&model_id= — list media for an entity.
- GET /media/{id} — show a media record.
- DELETE /media/{id} — soft delete (role-restricted).

All uploads/deletes require authentication and authorization via MediaPolicy (admin/content-manager). Configure your auth stack as needed (Sanctum/Passport/session).

## Environment
Copy `.env.example` to `.env` and set:

- MEDIA_PROVIDER=cloudinary|s3
- MEDIA_MAX_SIZE_MB=15

Cloudinary (preferred):
- CLOUDINARY_URL=cloudinary://<api_key>:<api_secret>@<cloud_name>
  or
- CLOUDINARY_CLOUD_NAME=
- CLOUDINARY_API_KEY=
- CLOUDINARY_API_SECRET=

S3:
- AWS_ACCESS_KEY_ID=, AWS_SECRET_ACCESS_KEY=, AWS_DEFAULT_REGION=, AWS_BUCKET=
- CDN_BASE_URL= optional CloudFront/base URL override

## Configuration
- config/media.php — provider, allowed mimes, size, and driver settings.
- Services: App\Services\MediaStorage\{CloudinaryStorage,S3Storage} through MediaStorage interface.
- Policy: App\Policies\MediaPolicy. Register roles via `hasRole('admin'|'content-manager')` or a `role` attribute on User.

## Notes
- Cloudinary driver returns transformation-ready secure URLs and stores public_id in metadata.
- S3 driver returns public URL or prefixed CDN_BASE_URL.
- Images should be served with widths/srcset on the frontend (see frontend components) to avoid CLS and optimize delivery.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo"></a></p>
