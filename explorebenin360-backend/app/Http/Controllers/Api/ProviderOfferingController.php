<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProviderOfferingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'isProvider') || !$user->isProvider()) {
            return response()->json(['message' => 'Provider account required'], 403);
        }
        $status = $request->get('status');
        $query = Offering::where('provider_id', $user->id)->withCount('bookings');
        if ($status) $query->where('status', $status);
        $offerings = $query->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data' => $offerings->map(function ($offering) {
                return [
                    'id' => $offering->id,
                    'title' => $offering->title,
                    'slug' => $offering->slug,
                    'type' => $offering->type,
                    'price' => $offering->price,
                    'currency' => $offering->currency,
                    'capacity' => $offering->capacity,
                    'status' => $offering->status,
                    'bookings_count' => $offering->bookings_count,
                    'cover_image_url' => $offering->cover_image_url,
                    'created_at' => $offering->created_at,
                    'updated_at' => $offering->updated_at,
                ];
            })
        ]);
    }

    public function show(Request $request, int $id)
    {
        $offering = Offering::findOrFail($id);
        $this->authorize('update', $offering);
        return response()->json([
            'data' => [
                'id' => $offering->id,
                'title' => $offering->title,
                'slug' => $offering->slug,
                'type' => $offering->type,
                'description' => $offering->description,
                'price' => $offering->price,
                'currency' => $offering->currency,
                'capacity' => $offering->capacity,
                'place_id' => $offering->place_id,
                'cover_image_url' => $offering->cover_image_url,
                'gallery' => $offering->gallery_json ?? [],
                'availability' => $offering->availability_json ?? [],
                'cancellation_policy' => $offering->cancellation_policy,
                'status' => $offering->status,
                'created_at' => $offering->created_at,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Offering::class);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:accommodation,experience,guide_service'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'place_id' => ['nullable', 'exists:places,id'],
            'cover_image_url' => ['nullable', 'url'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['array'],
            'cancellation_policy' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:draft,published'],
        ]);
        $user = $request->user();
        $offering = Offering::create([
            'provider_id' => $user->id,
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . Str::random(6),
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'XOF',
            'capacity' => $data['capacity'] ?? 1,
            'place_id' => $data['place_id'] ?? null,
            'cover_image_url' => $data['cover_image_url'] ?? null,
            'gallery_json' => $data['gallery'] ?? null,
            'cancellation_policy' => $data['cancellation_policy'] ?? null,
            'status' => $data['status'] ?? 'draft',
        ]);
        return response()->json([
            'data' => [
                'id' => $offering->id,
                'slug' => $offering->slug,
                'message' => 'Offering created successfully'
            ]
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $offering = Offering::findOrFail($id);
        $this->authorize('update', $offering);
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:accommodation,experience,guide_service'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'place_id' => ['nullable', 'exists:places,id'],
            'cover_image_url' => ['nullable', 'url'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['array'],
            'cancellation_policy' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'in:draft,published'],
        ]);
        if (isset($data['title']) && $data['title'] !== $offering->title) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
        }
        if (isset($data['gallery'])) {
            $data['gallery_json'] = $data['gallery'];
            unset($data['gallery']);
        }
        $offering->update($data);
        return response()->json([
            'data' => [
                'id' => $offering->id,
                'message' => 'Offering updated successfully'
            ]
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $offering = Offering::findOrFail($id);
        $this->authorize('delete', $offering);
        $activeBookings = $offering->bookings()->whereIn('status', ['pending', 'authorized', 'confirmed'])->count();
        if ($activeBookings > 0) {
            return response()->json(['message' => 'Cannot delete offering with active bookings'], 422);
        }
        $offering->delete();
        return response()->json(['message' => 'Offering deleted successfully']);
    }

    public function updateAvailability(Request $request, int $id)
    {
        $offering = Offering::findOrFail($id);
        $this->authorize('update', $offering);
        $data = $request->validate([
            'availability' => ['required', 'array'],
        ]);
        $offering->update(['availability_json' => $data['availability']]);
        return response()->json(['message' => 'Availability updated successfully']);
    }

    public function analytics(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'isProvider') || !$user->isProvider()) {
            return response()->json(['message' => 'Provider account required'], 403);
        }
        $offeringsCount = $user->offerings()->count();
        $publishedCount = $user->offerings()->where('status', 'published')->count();
        $bookingsStats = DB::table('bookings')
            ->join('offerings', 'bookings.offering_id', '=', 'offerings.id')
            ->where('offerings.provider_id', $user->id)
            ->selectRaw('
                COUNT(*) as total_bookings,
                SUM(CASE WHEN bookings.status = "confirmed" THEN 1 ELSE 0 END) as confirmed_bookings,
                SUM(CASE WHEN bookings.status = "confirmed" THEN bookings.amount ELSE 0 END) as total_revenue,
                SUM(CASE WHEN bookings.status = "confirmed" THEN (bookings.amount - bookings.commission_amount) ELSE 0 END) as net_revenue
            ')
            ->first();
        return response()->json([
            'data' => [
                'offerings' => [
                    'total' => $offeringsCount,
                    'published' => $publishedCount,
                ],
                'bookings' => [
                    'total' => (int) ($bookingsStats->total_bookings ?? 0),
                    'confirmed' => (int) ($bookingsStats->confirmed_bookings ?? 0),
                ],
                'revenue' => [
                    'gross' => (float) ($bookingsStats->total_revenue ?? 0),
                    'net' => (float) ($bookingsStats->net_revenue ?? 0),
                    'currency' => 'XOF',
                ],
            ]
        ]);
    }
}
