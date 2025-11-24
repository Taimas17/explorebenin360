<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlaceAdminController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable','string','max:200'],
            'status' => ['nullable','in:draft,published'],
            'type' => ['nullable','in:city,site,museum,park,beach,culture,history,gastronomy,adventure,other'],
            'city' => ['nullable','string','max:100'],
            'featured' => ['nullable','boolean'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);

        $query = Place::query();
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('title','like','%'.$q.'%')
                  ->orWhere('description','like','%'.$q.'%');
            });
        }
        if (!empty($data['status'])) $query->where('status', $data['status']);
        if (!empty($data['type'])) $query->where('type', $data['type']);
        if (!empty($data['city'])) $query->where('city', $data['city']);
        if (array_key_exists('featured', $data)) $query->where('featured', (bool)$data['featured']);
        $query->latest();

        $perPage = (int)($data['per_page'] ?? 15);
        $paginator = $query->paginate($perPage)->appends($request->query());
        return PlaceResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:places,slug'],
            'type' => ['required','in:city,site,museum,park,beach,culture,history,gastronomy,adventure,other'],
            'description' => ['required','string','max:5000'],
            'city' => ['required','string','max:100'],
            'country' => ['nullable','string','max:100'],
            'lat' => ['required','numeric','between:-90,90'],
            'lng' => ['required','numeric','between:-180,180'],
            'price_from' => ['nullable','numeric','min:0'],
            'opening_hours' => ['nullable','array'],
            'tags' => ['nullable','array'],
            'tags.*' => ['string'],
            'cover_image_url' => ['nullable','url'],
            'featured' => ['nullable','boolean'],
            'status' => ['nullable','in:draft,published'],
        ]);

        $place = new Place([
            'title' => $data['title'],
            'slug' => $data['slug'] ?? (Str::slug($data['title']).'-'.Str::random(6)),
            'type' => $data['type'],
            'description' => $data['description'],
            'city' => $data['city'],
            'country' => $data['country'] ?? 'Benin',
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'price_from' => $data['price_from'] ?? null,
            'opening_hours_json' => $data['opening_hours'] ?? null,
            'tags' => $data['tags'] ?? [],
            'cover_image_url' => $data['cover_image_url'] ?? null,
            'featured' => (bool)($data['featured'] ?? false),
            'status' => $data['status'] ?? 'published',
        ]);
        $place->save();

        return response()->json(['data' => new PlaceResource($place)], 201);
    }

    public function show(int $id)
    {
        $item = Place::findOrFail($id);
        return response()->json(['data' => new PlaceResource($item)]);
    }

    public function update(Request $request, int $id)
    {
        $item = Place::findOrFail($id);
        $data = $request->validate([
            'title' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','nullable','string','max:255','unique:places,slug,'.$item->id],
            'type' => ['sometimes','in:city,site,museum,park,beach,culture,history,gastronomy,adventure,other'],
            'description' => ['sometimes','string','max:5000'],
            'city' => ['sometimes','string','max:100'],
            'country' => ['sometimes','string','max:100'],
            'lat' => ['sometimes','numeric','between:-90,90'],
            'lng' => ['sometimes','numeric','between:-180,180'],
            'price_from' => ['sometimes','nullable','numeric','min:0'],
            'opening_hours' => ['sometimes','nullable','array'],
            'tags' => ['sometimes','array'],
            'tags.*' => ['string'],
            'cover_image_url' => ['sometimes','nullable','url'],
            'featured' => ['sometimes','boolean'],
            'status' => ['sometimes','in:draft,published'],
        ]);

        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']).'-'.Str::random(6);
        }
        if (array_key_exists('opening_hours', $data)) {
            $data['opening_hours_json'] = $data['opening_hours'];
            unset($data['opening_hours']);
        }
        $item->update($data);
        return response()->json(['data' => new PlaceResource($item)]);
    }

    public function destroy(int $id)
    {
        $item = Place::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Place deleted']);
    }
}
