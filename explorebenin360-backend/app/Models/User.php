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
        'name',
        'email',
        'password',
        'phone',
        'business_name',
        'bio',
        'avatar_url',
        'cover_image_url',
        'date_of_birth',
        'gender',
        'country',
        'city',
        'address',
        'postal_code',
        'website_url',
        'social_links',
        'preferences',
        'about_me',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'kyc_documents',
        'kyc_verified',
        'kyc_submitted',
        'provider_rejection_reason',
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
            'date_of_birth' => 'date',
            'social_links' => 'array',
            'preferences' => 'array',
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

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) return null;
        return $this->date_of_birth->age;
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([$this->city, $this->country]);
        return implode(', ', $parts);
    }
}
