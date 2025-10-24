<?php

namespace App\Providers;

use App\Services\MediaStorage\CloudinaryStorage;
use App\Services\MediaStorage\MediaStorage;
use App\Services\MediaStorage\S3Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MediaStorage::class, function () {
            $provider = Config::get('media.provider', 'cloudinary');
            return match ($provider) {
                's3' => new S3Storage(Config::get('media.s3.disk', 's3')),
                'local', 'public' => new S3Storage('public'),
                default => new CloudinaryStorage(),
            };
        });
    }

    public function boot(): void
    {
    }
}
