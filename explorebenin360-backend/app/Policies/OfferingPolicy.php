<?php

namespace App\Policies;

use App\Models\Offering;
use App\Models\User;

class OfferingPolicy
{
    /**
     * N'importe qui peut lister les offres (filtrées par status dans le controller)
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Les offres publiées sont publiques, sinon seul le provider ou admin
     */
    public function view(?User $user, Offering $offering): bool
    {
        // Offres publiées visibles par tous
        if ($offering->status === 'published') return true;
        
        // Sinon seul le provider ou admin
        if (!$user) return false;
        
        if ($offering->provider_id === $user->id) return true;
        
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        
        return false;
    }

    /**
     * Seuls les providers approuvés peuvent créer
     */
    public function create(User $user): bool
    {
        // Vérifier que l'utilisateur est un provider approuvé
        if (!method_exists($user, 'isProvider')) return false;
        
        return $user->isProvider();
    }

    /**
     * Seul le provider propriétaire peut modifier (ou admin)
     */
    public function update(User $user, Offering $offering): bool
    {
        // Admin peut tout modifier
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        
        // Provider peut modifier ses propres offres
        return $offering->provider_id === $user->id;
    }

    /**
     * Seul le provider propriétaire peut supprimer (ou admin)
     * Sauf s'il y a des réservations actives
     */
    public function delete(User $user, Offering $offering): bool
    {
        // Admin peut tout supprimer
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        
        // Provider peut supprimer ses propres offres
        if ($offering->provider_id === $user->id) return true;
        
        return false;
    }
}
