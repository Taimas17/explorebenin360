<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'reviewable_type' => ['required', 'in:accommodations,guides,places'],
            'reviewable_id' => ['required', 'integer'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = $data['per_page'] ?? 15;

        $modelClass = match($data['reviewable_type']) {
            'accommodations' => 'App\\Models\\Accommodation',
            'guides' => 'App\\Models\\Guide',
            'places' => 'App\\Models\\Place',
        };

        $table = match($data['reviewable_type']) {
            'accommodations' => 'accommodations',
            'guides' => 'guides',
            'places' => 'places',
        };

        if (!DB::table($table)->where('id', $data['reviewable_id'])->exists()) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $reviews = Review::query()
            ->where('reviewable_type', $modelClass)
            ->where('reviewable_id', $data['reviewable_id'])
            ->where('status', 'approved')
            ->with(['user:id,name,avatar_url'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $transformed = $reviews->getCollection()->map(function($review) {
            return [
                'id' => $review->id,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'avatar_url' => $review->user->avatar_url,
                ],
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->toISOString(),
            ];
        });

        return response()->json([
            'data' => $transformed,
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
            ]
        ]);
    }

    public function show(Request $request, int $id)
    {
        $review = Review::with(['user:id,name,avatar_url', 'booking'])->findOrFail($id);
        $this->authorize('view', $review);

        return response()->json([
            'data' => [
                'id' => $review->id,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'avatar_url' => $review->user->avatar_url,
                ],
                'booking_id' => $review->booking_id,
                'reviewable_type' => $review->reviewable_type,
                'reviewable_id' => $review->reviewable_id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'status' => $review->status,
                'created_at' => $review->created_at->toISOString(),
                'approved_at' => $review->approved_at?->toISOString(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Review::class);

        $data = $request->validate([
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $booking = Booking::with('offering')->findOrFail($data['booking_id']);

        if ($booking->user_id !== $user->id) {
            return response()->json(['message' => 'This booking does not belong to you'], 403);
        }
        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => 'Only confirmed bookings can be reviewed'], 422);
        }
        $existingReview = Review::where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return response()->json(['message' => 'You have already reviewed this booking'], 422);
        }

        $offering = $booking->offering;
        $reviewableType = null;
        $reviewableId = null;

        if ($offering && $offering->place_id) {
            $reviewableType = 'App\\Models\\Place';
            $reviewableId = $offering->place_id;
        } else {
            return response()->json(['message' => 'This offering cannot be reviewed (no associated entity)'], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'reviewable_type' => $reviewableType,
            'reviewable_id' => $reviewableId,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json(['data' => $review->load('user:id,name,avatar_url')], 201);
    }

    public function update(Request $request, int $id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('update', $review);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $review->update($data);
        return response()->json(['data' => $review]);
    }

    public function destroy(Request $request, int $id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('delete', $review);
        $review->delete();
        return response()->json(['message' => 'Review deleted']);
    }

    public function myReviews(Request $request)
    {
        $user = $request->user();
        $reviews = Review::where('user_id', $user->id)
            ->with(['reviewable', 'booking.offering'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(['data' => $reviews]);
    }

    public function canReview(Request $request, int $bookingId)
    {
        $user = $request->user();
        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== $user->id) {
            return response()->json(['can_review' => false, 'reason' => 'not_your_booking']);
        }
        if ($booking->status !== 'confirmed') {
            return response()->json(['can_review' => false, 'reason' => 'not_confirmed']);
        }
        $existingReview = Review::where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return response()->json(['can_review' => false, 'reason' => 'already_reviewed', 'review_id' => $existingReview->id]);
        }

        return response()->json(['can_review' => true]);
    }
}
