<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventAdminController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable','string','max:200'],
            'status' => ['nullable','in:draft,published'],
            'city' => ['nullable','string','max:100'],
            'category' => ['nullable','string','max:50'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);

        $query = Event::query()->with('place');
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('title','like','%'.$q.'%')
                  ->orWhere('description','like','%'.$q.'%');
            });
        }
        if (!empty($data['status'])) $query->where('status', $data['status']);
        if (!empty($data['city'])) $query->where('city', $data['city']);
        if (!empty($data['category'])) $query->where('category', $data['category']);
        $query->latest();

        $perPage = (int)($data['per_page'] ?? 15);
        $paginator = $query->paginate($perPage)->appends($request->query());
        return EventResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'place_id' => ['nullable','exists:places,id'],
            'title' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:events,slug'],
            'city' => ['required','string','max:100'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date','after_or_equal:start_date'],
            'organizer_name' => ['nullable','string','max:255'],
            'organizer_contact' => ['nullable','string','max:255'],
            'description' => ['required','string','max:5000'],
            'price' => ['nullable','numeric','min:0'],
            'currency' => ['nullable','string','size:3'],
            'category' => ['nullable','string','max:50'],
            'cover_image_url' => ['nullable','url'],
            'status' => ['nullable','in:draft,published'],
            'featured' => ['nullable','boolean'],
        ]);
        $validator->validate();
        $data = $validator->validated();

        $event = new Event([
            'place_id' => $data['place_id'] ?? null,
            'title' => $data['title'],
            'slug' => $data['slug'] ?? (Str::slug($data['title']).'-'.Str::random(6)),
            'city' => $data['city'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'organizer_name' => $data['organizer_name'] ?? null,
            'organizer_contact' => $data['organizer_contact'] ?? null,
            'description' => $data['description'],
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'] ?? 'XOF',
            'category' => $data['category'] ?? 'general',
            'cover_image_url' => $data['cover_image_url'] ?? null,
            'status' => $data['status'] ?? 'published',
            'featured' => (bool)($data['featured'] ?? false),
        ]);
        $event->save();
        $event->load('place');

        return response()->json(['data' => new EventResource($event)], 201);
    }

    public function show(int $id)
    {
        $item = Event::with('place')->findOrFail($id);
        return response()->json(['data' => new EventResource($item)]);
    }

    public function update(Request $request, int $id)
    {
        $item = Event::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'place_id' => ['sometimes','nullable','exists:places,id'],
            'title' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','nullable','string','max:255','unique:events,slug,'.$item->id],
            'city' => ['sometimes','string','max:100'],
            'start_date' => ['sometimes','date'],
            'end_date' => ['sometimes','date','after_or_equal:start_date'],
            'organizer_name' => ['sometimes','nullable','string','max:255'],
            'organizer_contact' => ['sometimes','nullable','string','max:255'],
            'description' => ['sometimes','string','max:5000'],
            'price' => ['sometimes','nullable','numeric','min:0'],
            'currency' => ['sometimes','string','size:3'],
            'category' => ['sometimes','string','max:50'],
            'cover_image_url' => ['sometimes','nullable','url'],
            'status' => ['sometimes','in:draft,published'],
            'featured' => ['sometimes','boolean'],
        ]);
        $validator->after(function($v) use ($request) {
            if ($request->filled('end_date') && !$request->filled('start_date')) {
                // ensure start_date exists if end_date provided and model has none yet
                // If model already has start_date, it's fine; we'll check logically after
            }
        });
        $validator->validate();
        $data = $validator->validated();

        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']).'-'.Str::random(6);
        }
        $item->update($data);
        $item->load('place');
        return response()->json(['data' => new EventResource($item)]);
    }

    public function destroy(int $id)
    {
        $item = Event::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Event deleted']);
    }
}
