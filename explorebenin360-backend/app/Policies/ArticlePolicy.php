<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(?User $user): bool { return true; }

    public function view(?User $user, Article $article): bool
    {
        if ($article->status === 'published') return true;
        if (!$user) return false;
        return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false;
    }

    public function create(User $user): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function update(User $user, Article $article): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function delete(User $user, Article $article): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }
}
