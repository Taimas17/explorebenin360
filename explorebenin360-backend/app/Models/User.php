<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'business_name', 'bio',
        'provider_status', 'kyc_submitted', 'kyc_verified', 'kyc_documents',
        'provider_rejection_reason', 'provider_approved_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'kyc_submitted' => 'boolean',
            'kyc_verified' => 'boolean',
            'kyc_documents' => 'array',
            'provider_approved_at' => 'datetime',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isProvider(): bool
    {
        return $this->provider_status === 'approved';
    }

    public function isProviderPending(): bool
    {
        return $this->provider_status === 'pending';
    }

    public function offerings()
    {
        return $this->hasMany(Offering::class, 'provider_id');
    }
}
