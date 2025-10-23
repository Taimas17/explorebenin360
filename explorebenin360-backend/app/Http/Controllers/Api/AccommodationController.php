<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccommodationResource;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccommodationController extends Controller
{
    public function index(Request $request)
    {
        $v = Validator::make($request->all(), [
            'q' => ['nullable','string','max:100'],
            'type' => ['nullable','in:hotel,guesthouse,ecolodge,bnb,other'],
            'city' => ['nullable','string','max:80'],
            'min_price' => ['nullable','numeric','min:0'],
            'max_price' => ['nullable','numeric','min:0'],
            'capacity' => ['nullable','integer','min:1'],
            'featured' => ['nullable','boolean'],
            'sort' => ['nullable','in:price,rating,recent'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);
        $data = $v->validated();

        $query = Accommodation::query()->where('status','published');
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('title','like','%'.$q.'%')
                  ->orWhere('description','like','%'.$q.'%');
            });
        }
        if (!empty($data['type'])) $query->where('type', $data['type']);
        if (!empty($data['city'])) $query->where('city', $data['city']);
        if (!empty($data['min_price'])) $query->where('price_per_night','>=',$data['min_price']);
        if (!empty($data['max_price'])) $query->where('price_per_night','<=',$data['max_price']);
        if (!empty($data['capacity'])) $query->where('capacity','>=',$data['capacity']);
        if (array_key_exists('featured', $data)) $query->where('featured', (bool)$data['featured']);

        $sort = $data['sort'] ?? null;
        if ($sort === 'rating') $query->orderByDesc('rating_avg');
        elseif ($sort === 'price') $query->orderBy('price_per_night');
        elseif ($sort === 'recent') $query->latest();
        else $query->orderByDesc('featured')->orderBy('title');

        $perPage = (int)($data['per_page'] ?? 15);
        if ($perPage > 50) $perPage = 50;
        $paginator = $query->paginate($perPage)->appends($request->query());
        return AccommodationResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function show(string $slug)
    {
        $item = Accommodation::where('slug',$slug)->where('status','published')->firstOrFail();
        return new AccommodationResource($item);
    }
}
