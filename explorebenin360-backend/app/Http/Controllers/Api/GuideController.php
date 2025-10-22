<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GuideResource;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $v = Validator::make($request->all(), [
            'q' => ['nullable','string','max:100'],
            'city' => ['nullable','string','max:80'],
            'language' => ['nullable','string','max:20'],
            'specialty' => ['nullable','string','max:30'],
            'verified' => ['nullable','boolean'],
            'min_price' => ['nullable','numeric','min:0'],
            'max_price' => ['nullable','numeric','min:0'],
            'sort' => ['nullable','in:rating,price,recent'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);
        $data = $v->validated();

        $query = Guide::query()->where('status','published');
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('name','like','%'.$q.'%')
                  ->orWhere('bio','like','%'.$q.'%')
                  ->orWhereJsonContains('specialties_json', $q);
            });
        }
        if (!empty($data['city'])) $query->where('city', $data['city']);
        if (!empty($data['language'])) $query->whereJsonContains('languages_json', $data['language']);
        if (!empty($data['specialty'])) $query->whereJsonContains('specialties_json', $data['specialty']);
        if (array_key_exists('verified', $data)) $query->where('verified', (bool)$data['verified']);
        if (!empty($data['min_price'])) $query->where('price_per_day','>=',$data['min_price']);
        if (!empty($data['max_price'])) $query->where('price_per_day','<=',$data['max_price']);

        $sort = $data['sort'] ?? null;
        if ($sort === 'rating') $query->orderByDesc('rating_avg');
        elseif ($sort === 'price') $query->orderBy('price_per_day');
        elseif ($sort === 'recent') $query->latest();
        else $query->orderByDesc('verified')->orderBy('name');

        $perPage = (int)($data['per_page'] ?? 15);
        if ($perPage > 50) $perPage = 50;
        $paginator = $query->paginate($perPage)->appends($request->query());
        return GuideResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function show(string $slug)
    {
        $item = Guide::where('slug',$slug)->where('status','published')->firstOrFail();
        return new GuideResource($item);
    }
}
