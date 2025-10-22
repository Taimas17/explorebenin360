# Cloudinary Configuration Checklist (Staging)

A) Account & Auth
- Cloud name, API key, API secret (do NOT commit values)
- Restrict API credentials to staging usage if possible

B) Upload strategy
- Server-side signed uploads only for now (admin/content roles)
- Create base folder: `explorebenin360/staging` (subfolders: `brand`, `places`, `hebergements`, `guides`, `blog`)
- Max upload size = 15 MB (align with `MEDIA_MAX_SIZE_MB`)
- Allowed types = `jpg,jpeg,png,webp,avif,mp4`

C) Delivery & Transformations
- Use `https://res.cloudinary.com/<cloud_name>/` for delivery
- Default params: `f=auto, q=auto`
- Common widths: `400, 800, 1200, 1600`; DPR-aware (1x/2x)
- For hero/OG: PNG/JPEG fallback if AVIF not supported

D) Security & Governance
- No unsigned public presets for now (avoid client-side uploads)
- If an unsigned preset is needed later: restrict by whitelisted domains and size/type limits
- EXIF stripping enabled by default; sensitive metadata removed
- Naming convention: `eb360/{type}/{slug}/{filename}`
- Versioning/overwrite policy defined; purge old assets when replacing

E) Observability
- Monitor quota/usage on Cloudinary dashboard (bandwidth, transformations)
- Set alerts for thresholds (optional)

F) Testing
- Once env is set, validate with a simple backend upload (POST `/api/v1/media`) and a frontend render via `EBImage`
