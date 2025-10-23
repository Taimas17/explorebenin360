Local environment notes

Backend (Laravel):
- Use local media storage
  - In explorebenin360-backend/.env set:
    MEDIA_PROVIDER=local
    FILESYSTEM_DISK=public
- Paystack in TEST mode
  - PAYSTACK_PUBLIC_KEY=pk_test_xxx
  - PAYSTACK_SECRET_KEY=sk_test_xxx
  - PAYSTACK_BASE_URL=https://api.paystack.co
  - FRONTEND_URL=http://localhost:5173
- Commission (optional)
  - COMMISSION_PERCENT=12

Run tests locally:
- php artisan migrate --env=testing
- php artisan test

Frontend (Vite + Vue):
- API base URL
  - Create explorebenin360-frontend/.env.local
    VITE_API_BASE_URL=/api/v1
    VITE_USE_STUBS=false
- Media (optional Cloudinary transforms)
  - VITE_CLOUDINARY_CLOUD_NAME= (leave empty for local)

Roles & access:
- Travelers created via registration get role "traveler" by default.
- Assign provider/admin roles manually for local testing (tinker or seeder).
- Route guards enforce access by role for /dashboard (traveler), /provider (provider), /admin (admin).
