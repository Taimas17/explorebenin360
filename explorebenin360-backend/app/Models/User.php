<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'business_name',
        'bio',
        // Admin management fields
        'account_status',
        'suspension_reason',
        // Profile fields
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
        'suspended_by',
        'last_login_ip',
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
            // Admin management
            'suspended_at' => 'datetime',
            'last_login_at' => 'datetime',
            'login_count' => 'integer',
            // Profile
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

    public function offerings()
    {
        return $this->hasMany(Offering::class, 'provider_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function threadsAsProvider()
    {
        return $this->hasMany(MessageThread::class, 'provider_id');
    }

    public function threadsAsTraveler()
    {
        return $this->hasMany(MessageThread::class, 'traveler_id');
    }

    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function scopeActive($query)
    {
        return $query->where('account_status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('account_status', 'suspended');
    }

    public function scopeBanned($query)
    {
        return $query->where('account_status', 'banned');
    }

    public function scopeHasRole($query, $role)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', $role));
    }

    public function isSuspended(): bool
    {
        return $this->account_status === 'suspended';
    }

    public function isBanned(): bool
    {
        return $this->account_status === 'banned';
    }

    public function isActive(): bool
    {
        return $this->account_status === 'active';
    }

    public function isProvider(): bool
    {
        return $this->provider_status === 'approved';
    }

    public function isProviderPending(): bool
    {
        return $this->provider_status === 'pending';
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
