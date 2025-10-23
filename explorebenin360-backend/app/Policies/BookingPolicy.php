<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        if ($booking->user_id === $user->id) return true;
        if ($booking->relationLoaded('offering')) {
            return $booking->offering?->provider_id === $user->id;
        }
        return $booking->offering()->value('provider_id') === $user->id;
    }

    public function update(User $user, Booking $booking): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        // Providers can update their own bookings
        if ($booking->relationLoaded('offering')) {
            return $booking->offering?->provider_id === $user->id;
        }
        return $booking->offering()->value('provider_id') === $user->id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        if ($booking->user_id === $user->id) return true;
        // Providers can cancel their own bookings
        if ($booking->relationLoaded('offering')) {
            return $booking->offering?->provider_id === $user->id;
        }
        return $booking->offering()->value('provider_id') === $user->id;
    }
}
