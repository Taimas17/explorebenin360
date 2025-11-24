<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RuntimeException;

class EnvironmentValidationProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (app()->environment('production')) {
            $this->validateProduction();
        }
    }
    
    protected function validateProduction(): void
    {
        $errors = [];
        
        if (empty(env('APP_KEY'))) {
            $errors[] = 'APP_KEY is empty';
        }
        
        if (env('APP_DEBUG')) {
            $errors[] = 'APP_DEBUG must be false in production';
        }
        
        if (empty(env('FRONTEND_ORIGIN')) || env('FRONTEND_ORIGIN') === '*') {
            $errors[] = 'FRONTEND_ORIGIN must be explicitly set (not *)';
        }
        
        if (!empty($errors)) {
            throw new RuntimeException(
                'SECURITY ERROR - Production environment misconfigured: ' . 
                implode(', ', $errors)
            );
        }
    }
}
