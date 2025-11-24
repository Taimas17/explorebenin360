<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleAdminController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable','string','max:200'],
            'status' => ['nullable','in:draft,published'],
            'category' => ['nullable','string','max:50'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);

        $query = Article::query();
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('title','like','%'.$q.'%')
                  ->orWhere('excerpt','like','%'.$q.'%')
                  ->orWhere('body','like','%'.$q.'%');
            });
        }
        if (!empty($data['status'])) $query->where('status', $data['status']);
        if (!empty($data['category'])) $query->where('category', $data['category']);
        $query->latest();

        $perPage = (int)($data['per_page'] ?? 15);
        $paginator = $query->paginate($perPage)->appends($request->query());
        return ArticleResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:articles,slug'],
            'excerpt' => ['required','string','max:500'],
            'body' => ['required','string','max:50000'],
            'author_name' => ['required','string','max:100'],
            'category' => ['nullable','string','max:50'],
            'tags' => ['nullable','array'],
            'tags.*' => ['string'],
            'cover_image_url' => ['nullable','url'],
            'status' => ['nullable','in:draft,published'],
            'published_at' => ['nullable','date'],
        ]);

        $article = new Article([
            'title' => $data['title'],
            'slug' => $data['slug'] ?? (Str::slug($data['title']).'-'.Str::random(6)),
            'excerpt' => $data['excerpt'],
            'body' => $data['body'],
            'author_name' => $data['author_name'],
            'category' => $data['category'] ?? 'general',
            'tags' => $data['tags'] ?? [],
            'cover_image_url' => $data['cover_image_url'] ?? null,
            'status' => $data['status'] ?? 'published',
            'published_at' => $data['published_at'] ?? null,
        ]);
        if ($article->status === 'published' && $article->published_at === null) {
            $article->published_at = now();
        }
        $article->save();

        return response()->json(['data' => new ArticleResource($article)], 201);
    }

    public function show(int $id)
    {
        $item = Article::findOrFail($id);
        return response()->json(['data' => new ArticleResource($item)]);
    }

    public function update(Request $request, int $id)
    {
        $item = Article::findOrFail($id);
        $data = $request->validate([
            'title' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','nullable','string','max:255','unique:articles,slug,'.$item->id],
            'excerpt' => ['sometimes','string','max:500'],
            'body' => ['sometimes','string','max:50000'],
            'author_name' => ['sometimes','string','max:100'],
            'category' => ['sometimes','string','max:50'],
            'tags' => ['sometimes','array'],
            'tags.*' => ['string'],
            'cover_image_url' => ['sometimes','nullable','url'],
            'status' => ['sometimes','in:draft,published'],
            'published_at' => ['sometimes','nullable','date'],
        ]);

        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']).'-'.Str::random(6);
        }
        $wasDraft = $item->status === 'draft';
        $item->fill($data);
        if ($wasDraft && $item->status === 'published' && empty($item->published_at)) {
            $item->published_at = now();
        }
        $item->save();

        return response()->json(['data' => new ArticleResource($item)]);
    }

    public function destroy(int $id)
    {
        $item = Article::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Article deleted']);
    }
}
