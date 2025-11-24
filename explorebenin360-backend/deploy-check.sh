#!/bin/bash
set -e

echo "=== ExploreBenin360 Deployment Checklist ==="

# Vérifier les variables d'environnement
php artisan env:validate || exit 1

# Vérifier que la clé est générée
if grep -q "APP_KEY=$" .env; then
    echo "ERROR: APP_KEY is empty. Run: php artisan key:generate"
    exit 1
fi

# Vérifier APP_DEBUG
if grep -q "APP_DEBUG=true" .env; then
    echo "ERROR: APP_DEBUG must be false in production"
    exit 1
fi

# Vérifier FRONTEND_ORIGIN
if grep -q "FRONTEND_ORIGIN=\*" .env; then
    echo "ERROR: FRONTEND_ORIGIN cannot be wildcard"
    exit 1
fi

echo "✓ Environment validation passed"
echo "✓ Ready for deployment"
