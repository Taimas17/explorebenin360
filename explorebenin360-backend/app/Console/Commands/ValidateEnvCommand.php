<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidateEnvCommand extends Command
{
    protected $signature = 'env:validate';
    protected $description = 'Validate required environment variables';

    public function handle()
    {
        $required = [
            'APP_KEY' => 'Application encryption key missing',
            'DB_HOST' => 'Database host not configured',
            'DB_DATABASE' => 'Database name not configured',
            'DB_USERNAME' => 'Database username not configured',
            'FRONTEND_ORIGIN' => 'Frontend origin not configured (CORS)',
        ];
        
        $conditionalRequired = [
            'PAYSTACK_PUBLIC_KEY' => env('APP_ENV') === 'production',
            'PAYSTACK_SECRET_KEY' => env('APP_ENV') === 'production',
            'CLOUDINARY_CLOUD_NAME' => env('MEDIA_PROVIDER') === 'cloudinary',
            'CLOUDINARY_API_KEY' => env('MEDIA_PROVIDER') === 'cloudinary',
            'CLOUDINARY_API_SECRET' => env('MEDIA_PROVIDER') === 'cloudinary',
        ];
        
        $errors = [];
        
        foreach ($required as $key => $message) {
            if (empty(env($key))) {
                $errors[] = "$key: $message";
            }
        }
        
        foreach ($conditionalRequired as $key => $condition) {
            if ($condition && empty(env($key))) {
                $errors[] = "$key: Required for current configuration";
            }
        }
        
        if (env('APP_ENV') === 'production') {
            if (env('APP_DEBUG') === true || env('APP_DEBUG') === 'true') {
                $errors[] = 'APP_DEBUG: Must be false in production';
            }
            
            if (empty(env('DB_PASSWORD'))) {
                $this->warn('DB_PASSWORD: Empty database password in production');
            }
            
            if (env('FRONTEND_ORIGIN') === '*') {
                $errors[] = 'FRONTEND_ORIGIN: Cannot use wildcard in production';
            }
        }
        
        if (!empty($errors)) {
            $this->error('Environment validation failed:');
            foreach ($errors as $error) {
                $this->error('  - ' . $error);
            }
            return 1;
        }
        
        $this->info('✓ All environment variables are properly configured');
        return 0;
    }
}
