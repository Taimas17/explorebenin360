<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        $v = Validator::make($request->all(), [
            'q' => ['nullable','string','max:100'],
            'type' => ['nullable','in:city,site,museum,park,beach,culture,history,gastronomy,adventure,other'],
            'city' => ['nullable','string','max:80'],
            'tag' => ['nullable','string','max:50'],
            'featured' => ['nullable','boolean'],
            'min_price' => ['nullable','numeric','min:0'],
            'max_price' => ['nullable','numeric','min:0'],
            'bounds' => ['nullable','string'],
            'sort' => ['nullable','in:relevance,rating,price,recent'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);
        $data = $v->validated();

        $query = Place::query()->where('status','published');

        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('title','like','%'.$q.'%')
                  ->orWhere('description','like','%'.$q.'%')
                  ->orWhereJsonContains('tags', $q);
            });
        }
        if (!empty($data['type'])) $query->where('type', $data['type']);
        if (!empty($data['city'])) $query->where('city', $data['city']);
        if (!empty($data['tag'])) $query->whereJsonContains('tags', $data['tag']);
        if (array_key_exists('featured', $data)) $query->where('featured', (bool)$data['featured']);
        if (!empty($data['min_price'])) $query->where('price_from','>=',$data['min_price']);
        if (!empty($data['max_price'])) $query->where('price_from','<=',$data['max_price']);
        if (!empty($data['bounds'])) {
            $parts = array_map('trim', explode(',', $data['bounds']));
            if (count($parts) === 4) {
                [$swLat,$swLng,$neLat,$neLng] = $parts;
                $query->whereBetween('lat', [(float)$swLat, (float)$neLat])
                      ->whereBetween('lng', [(float)$swLng, (float)$neLng]);
            }
        }

        $sort = $data['sort'] ?? null;
        if ($sort === 'rating') $query->orderByDesc('rating_avg');
        elseif ($sort === 'price') $query->orderBy('price_from')->orderByDesc('featured');
        elseif ($sort === 'recent') $query->latest();
        else $query->orderByDesc('featured')->orderBy('title');

        $perPage = (int)($data['per_page'] ?? 15);
        if ($perPage > 50) $perPage = 50;

        $paginator = $query->paginate($perPage)->appends($request->query());
        return PlaceResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function show(string $slug)
    {
        $place = Place::where('slug',$slug)->where('status','published')->firstOrFail();
        return new PlaceResource($place);
    }
}
