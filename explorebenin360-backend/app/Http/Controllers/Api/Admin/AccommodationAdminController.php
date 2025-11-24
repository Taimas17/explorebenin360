<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccommodationResource;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccommodationAdminController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable','string','max:100'],
            'status' => ['nullable','in:draft,published'],
            'city' => ['nullable','string','max:100'],
            'type' => ['nullable','in:hotel,guesthouse,ecolodge,bnb,other'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);

        $query = Accommodation::query();
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('title','like','%'.$q.'%')
                  ->orWhere('description','like','%'.$q.'%');
            });
        }
        if (!empty($data['status'])) $query->where('status', $data['status']);
        if (!empty($data['city'])) $query->where('city', $data['city']);
        if (!empty($data['type'])) $query->where('type', $data['type']);

        $query->latest();
        $perPage = (int)($data['per_page'] ?? 15);
        $paginator = $query->paginate($perPage)->appends($request->query());
        return AccommodationResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id' => ['nullable','exists:places,id'],
            'title' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:accommodations,slug'],
            'type' => ['required','in:hotel,guesthouse,ecolodge,bnb,other'],
            'description' => ['required','string','max:5000'],
            'address' => ['required','string','max:255'],
            'city' => ['required','string','max:100'],
            'lat' => ['required','numeric','between:-90,90'],
            'lng' => ['required','numeric','between:-180,180'],
            'price_per_night' => ['required','numeric','min:0'],
            'currency' => ['nullable','string','size:3'],
            'amenities' => ['nullable','array'],
            'amenities.*' => ['string'],
            'capacity' => ['nullable','integer','min:1'],
            'featured' => ['nullable','boolean'],
            'status' => ['nullable','in:draft,published'],
            'cover_image_url' => ['nullable','url','max:500'],
        ]);

        $accommodation = new Accommodation([
            'place_id' => $data['place_id'] ?? null,
            'title' => $data['title'],
            'slug' => $data['slug'] ?? (Str::slug($data['title']).'-'.Str::random(6)),
            'type' => $data['type'],
            'description' => $data['description'],
            'address' => $data['address'],
            'city' => $data['city'],
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'price_per_night' => $data['price_per_night'],
            'currency' => $data['currency'] ?? 'XOF',
            'amenities_json' => $data['amenities'] ?? null,
            'capacity' => $data['capacity'] ?? 2,
            'featured' => (bool)($data['featured'] ?? false),
            'status' => $data['status'] ?? 'published',
            'cover_image_url' => $data['cover_image_url'] ?? null,
        ]);
        $accommodation->save();
        $accommodation->load('place');

        return response()->json(['data' => new AccommodationResource($accommodation)], 201);
    }

    public function show(int $id)
    {
        $item = Accommodation::with('place')->findOrFail($id);
        return response()->json(['data' => new AccommodationResource($item)]);
    }

    public function update(Request $request, int $id)
    {
        $item = Accommodation::findOrFail($id);
        $data = $request->validate([
            'place_id' => ['sometimes','nullable','exists:places,id'],
            'title' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','nullable','string','max:255','unique:accommodations,slug,'.$item->id],
            'type' => ['sometimes','in:hotel,guesthouse,ecolodge,bnb,other'],
            'description' => ['sometimes','string','max:5000'],
            'address' => ['sometimes','string','max:255'],
            'city' => ['sometimes','string','max:100'],
            'lat' => ['sometimes','numeric','between:-90,90'],
            'lng' => ['sometimes','numeric','between:-180,180'],
            'price_per_night' => ['sometimes','numeric','min:0'],
            'currency' => ['sometimes','string','size:3'],
            'amenities' => ['sometimes','nullable','array'],
            'amenities.*' => ['string'],
            'capacity' => ['sometimes','integer','min:1'],
            'featured' => ['sometimes','boolean'],
            'status' => ['sometimes','in:draft,published'],
            'cover_image_url' => ['sometimes','nullable','url','max:500'],
        ]);

        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']).'-'.Str::random(6);
        }
        if (array_key_exists('amenities', $data)) {
            $data['amenities_json'] = $data['amenities'];
            unset($data['amenities']);
        }

        $item->update($data);
        $item->load('place');
        return response()->json(['data' => new AccommodationResource($item)]);
    }

    public function destroy(int $id)
    {
        $item = Accommodation::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Accommodation deleted']);
    }
}
