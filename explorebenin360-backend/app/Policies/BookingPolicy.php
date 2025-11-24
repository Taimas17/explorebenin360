<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Le client ou le provider peut voir la réservation
     */
    public function view(User $user, Booking $booking): bool
    {
        // Le client peut voir sa propre réservation
        if ($booking->user_id === $user->id) return true;
        
        // Le provider peut voir les réservations de ses offres
        if ($booking->relationLoaded('offering') && $booking->offering) {
            if ($booking->offering->provider_id === $user->id) return true;
        } else {
            // Charger la relation si nécessaire
            $providerId = $booking->offering()->value('provider_id');
            if ($providerId === $user->id) return true;
        }
        
        // Admin peut tout voir
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        
        return false;
    }

    /**
     * Seuls le provider ET l'admin peuvent update
     * Le client ne peut PAS update directement
     */
    public function update(User $user, Booking $booking): bool
    {
        // Admin peut tout modifier
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        
        // Provider peut modifier les réservations de ses offres
        if ($booking->relationLoaded('offering') && $booking->offering) {
            return $booking->offering->provider_id === $user->id;
        }
        
        $providerId = $booking->offering()->value('provider_id');
        return $providerId === $user->id;
    }

    /**
     * Seul le client peut annuler sa propre réservation
     * (sauf si statut déjà cancelled/refunded)
     */
    public function cancel(User $user, Booking $booking): bool
    {
        // Vérifier que c'est bien le client
        if ($booking->user_id !== $user->id) return false;
        
        // Vérifier que le statut permet l'annulation
        if (in_array($booking->status, ['cancelled', 'refunded'])) {
            return false;
        }
        
        return true;
    }
}
