<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;


class PlaceController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'type' => ['nullable', 'string', 'in:city,site,museum,park,beach,culture,history,gastronomy,adventure,other'],
            'city' => ['nullable', 'string', 'max:100'],
            'featured' => ['nullable', 'boolean'],
            'bounds' => ['nullable', 'string', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?,-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Place::query()->where('status', 'published');

        if (!empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where('title', 'like', '%' . $data['q'] . '%')
                  ->orWhere('description', 'like', '%' . $data['q'] . '%');
            });
        }

        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
        }

        if (!empty($data['city'])) {
            $query->where('city', 'like', '%' . $data['city'] . '%');
        }

        if (isset($data['featured'])) {
            $query->where('featured', $data['featured']);
        }

        if (!empty($data['bounds'])) {
            $parts = array_map('floatval', explode(',', $data['bounds']));
            
            if (count($parts) === 4) {
                [$swLat, $swLng, $neLat, $neLng] = $parts;
                
                if ($swLat >= -90 && $swLat <= 90 && $neLat >= -90 && $neLat <= 90 &&
                    $swLng >= -180 && $swLng <= 180 && $neLng >= -180 && $neLng <= 180 &&
                    $swLat < $neLat && $swLng < $neLng) {
                    
                    $query->whereBetween('lat', [$swLat, $neLat])
                          ->whereBetween('lng', [$swLng, $neLng]);
                }
            }
        }

        $perPage = $data['per_page'] ?? 15;
        $paginator = $query->paginate($perPage);

        return response()->json($paginator);
    }

    public function show(string $slug)
    {
        $place = Place::where('slug',$slug)->where('status','published')->firstOrFail();
        return new PlaceResource($place);
    }
}
