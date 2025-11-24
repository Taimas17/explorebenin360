<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Place;
use App\Models\Accommodation;
use App\Models\Article;
use App\Models\Guide;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $favorites = Favorite::where('user_id', $user->id)
            ->get()
            ->groupBy('type')
            ->map(fn($items) => $items->pluck('item_id')->toArray());

        $grouped = [
            'destination' => $favorites->get('destination', []),
            'hebergement' => $favorites->get('hebergement', []),
            'article' => $favorites->get('article', []),
            'guide' => $favorites->get('guide', []),
        ];

        return response()->json(['data' => $grouped]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:destination,hebergement,article,guide'],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        // Vérifier l'existence selon le type
        if (!$this->checkItemExists($data['type'], $data['id'])) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Vérifier que ce n'est pas déjà un favori (éviter duplicate)
        $exists = Favorite::where('user_id', $request->user()->id)
            ->where('type', $data['type'])
            ->where('item_id', $data['id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Already favorited'], 200);
        }

        $favorite = Favorite::create([
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'item_id' => $data['id'],
        ]);

        return response()->json(['data' => $favorite], 201);
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:destination,hebergement,article,guide'],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $deleted = Favorite::where('user_id', $request->user()->id)
            ->where('type', $data['type'])
            ->where('item_id', $data['id'])
            ->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Favorite not found'], 404);
        }

        return response()->json(['message' => 'Removed'], 200);
    }

    private function checkItemExists(string $type, int $id): bool
    {
        return match($type) {
            'destination' => Place::where('id', $id)->exists(),
            'hebergement' => Accommodation::where('id', $id)->exists(),
            'article' => Article::where('id', $id)->exists(),
            'guide' => Guide::where('id', $id)->exists(),
            default => false
        };
    }
}
