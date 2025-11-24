<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    /**
     * Seuls les utilisateurs authentifiés peuvent voir les médias
     * Exception: médias publiques liées à du contenu publié
     */
    public function viewAny(?User $user): bool
    {
        // Les médias publiques (liées à du contenu publié) sont visibles sans auth
        // Mais requérir auth pour lister TOUS les médias
        return $user !== null;
    }

    /**
     * Vérifier si l'utilisateur peut voir un média spécifique
     */
    public function view(?User $user, Media $media): bool
    {
        // Si le média est lié à du contenu publié, c'est public
        if ($media->model_type && $media->model_id) {
            // Vérifier si le modèle parent est publié
            $modelClass = $media->model_type;
            if (class_exists($modelClass)) {
                $parent = $modelClass::find($media->model_id);
                if ($parent && isset($parent->status) && $parent->status === 'published') {
                    return true;
                }
            }
        }
        
        // Sinon, seul le créateur ou un admin peut voir
        if (!$user) return false;
        
        if ($media->created_by === $user->id) return true;
        
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        
        return false;
    }

    /**
     * Seuls admin et content-manager peuvent créer
     */
    public function create(User $user): bool
    {
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('admin') || $user->hasRole('content-manager');
        }
        
        return false;
    }

    /**
     * Seul le créateur ou un admin peut supprimer
     */
    public function delete(User $user, Media $media): bool
    {
        // Le créateur peut supprimer son propre média
        if ($media->created_by === $user->id) {
            return true;
        }
        
        // Admin peut tout supprimer
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        
        return false;
    }
}
