<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $v = Validator::make($request->all(), [
            'q' => ['nullable','string','max:100'],
            'category' => ['nullable','string','max:50'],
            'tag' => ['nullable','string','max:50'],
            'sort' => ['nullable','in:recent,popular'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);
        $data = $v->validated();

        $query = Article::query()->where('status','published');
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('title','like','%'.$q.'%')
                  ->orWhere('excerpt','like','%'.$q.'%')
                  ->orWhere('body','like','%'.$q.'%')
                  ->orWhereJsonContains('tags', $q);
            });
        }
        if (!empty($data['category'])) $query->where('category', $data['category']);
        if (!empty($data['tag'])) $query->whereJsonContains('tags', $data['tag']);
        $sort = $data['sort'] ?? null;
        if ($sort === 'recent') $query->orderByDesc('published_at');
        else $query->latest('published_at');

        $perPage = (int)($data['per_page'] ?? 15);
        if ($perPage > 50) $perPage = 50;
        $paginator = $query->paginate($perPage)->appends($request->query());
        return ArticleResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function show(string $slug)
    {
        $item = Article::where('slug',$slug)->where('status','published')->firstOrFail();
        return new ArticleResource($item);
    }
}
