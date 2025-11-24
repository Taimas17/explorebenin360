<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GuideResource;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuideAdminController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable','string','max:200'],
            'status' => ['nullable','in:draft,published'],
            'city' => ['nullable','string','max:100'],
            'verified' => ['nullable','boolean'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);

        $query = Guide::query();
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('name','like','%'.$q.'%')
                  ->orWhere('bio','like','%'.$q.'%');
            });
        }
        if (!empty($data['status'])) $query->where('status', $data['status']);
        if (!empty($data['city'])) $query->where('city', $data['city']);
        if (array_key_exists('verified', $data)) $query->where('verified', (bool)$data['verified']);
        $query->latest();

        $perPage = (int)($data['per_page'] ?? 15);
        $paginator = $query->paginate($perPage)->appends($request->query());
        return GuideResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:guides,slug'],
            'languages' => ['nullable','array','min:1'],
            'languages.*' => ['string'],
            'specialties' => ['nullable','array'],
            'specialties.*' => ['string'],
            'bio' => ['required','string','max:2000'],
            'avatar_url' => ['nullable','url'],
            'city' => ['required','string','max:100'],
            'lat' => ['nullable','numeric','between:-90,90'],
            'lng' => ['nullable','numeric','between:-180,180'],
            'price_per_day' => ['nullable','numeric','min:0'],
            'currency' => ['nullable','string','size:3'],
            'verified' => ['nullable','boolean'],
            'status' => ['nullable','in:draft,published'],
        ]);

        $guide = new Guide([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? (Str::slug($data['name']).'-'.Str::random(6)),
            'languages_json' => $data['languages'] ?? [],
            'specialties_json' => $data['specialties'] ?? [],
            'bio' => $data['bio'],
            'avatar_url' => $data['avatar_url'] ?? null,
            'city' => $data['city'],
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'price_per_day' => $data['price_per_day'] ?? null,
            'currency' => $data['currency'] ?? 'XOF',
            'verified' => (bool)($data['verified'] ?? false),
            'status' => $data['status'] ?? 'published',
        ]);
        $guide->save();

        return response()->json(['data' => new GuideResource($guide)], 201);
    }

    public function show(int $id)
    {
        $item = Guide::findOrFail($id);
        return response()->json(['data' => new GuideResource($item)]);
    }

    public function update(Request $request, int $id)
    {
        $item = Guide::findOrFail($id);
        $data = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','nullable','string','max:255','unique:guides,slug,'.$item->id],
            'languages' => ['sometimes','array','min:1'],
            'languages.*' => ['string'],
            'specialties' => ['sometimes','array'],
            'specialties.*' => ['string'],
            'bio' => ['sometimes','string','max:2000'],
            'avatar_url' => ['sometimes','nullable','url'],
            'city' => ['sometimes','string','max:100'],
            'lat' => ['sometimes','nullable','numeric','between:-90,90'],
            'lng' => ['sometimes','nullable','numeric','between:-180,180'],
            'price_per_day' => ['sometimes','nullable','numeric','min:0'],
            'currency' => ['sometimes','string','size:3'],
            'verified' => ['sometimes','boolean'],
            'status' => ['sometimes','in:draft,published'],
        ]);

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        }
        if (array_key_exists('languages', $data)) {
            $data['languages_json'] = $data['languages'];
            unset($data['languages']);
        }
        if (array_key_exists('specialties', $data)) {
            $data['specialties_json'] = $data['specialties'];
            unset($data['specialties']);
        }
        $item->update($data);
        return response()->json(['data' => new GuideResource($item)]);
    }

    public function destroy(int $id)
    {
        $item = Guide::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Guide deleted']);
    }
}
