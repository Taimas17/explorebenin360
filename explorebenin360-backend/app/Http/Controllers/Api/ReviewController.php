<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request, int $offeringId)
    {
        $perPage = min((int)$request->get('per_page', 20), 50);
        
        $reviews = Review::where('offering_id', $offeringId)
            ->published()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        return response()->json([
            'data' => $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'photos' => $review->photos_json ?? [],
                    'user' => [
                        'id' => $review->user->id,
                        'name' => $review->user->name,
                    ],
                    'created_at' => $review->created_at,
                ];
            }),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Review::class);
        
        $data = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['url']
        ]);
        
        $user = $request->user();
        $booking = Booking::with('offering')->findOrFail($data['booking_id']);
        
        if ($booking->user_id !== $user->id) {
            return response()->json(['message' => 'This booking does not belong to you'], 403);
        }
        
        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => 'Can only review confirmed bookings'], 422);
        }
        
        if ($booking->review()->exists()) {
            return response()->json(['message' => 'Booking already reviewed'], 422);
        }
        
        $review = Review::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'offering_id' => $booking->offering_id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'photos_json' => $data['photos'] ?? null,
            'status' => 'pending',
        ]);
        
        return response()->json([
            'data' => [
                'id' => $review->id,
                'status' => $review->status,
                'message' => 'Review submitted for moderation'
            ]
        ], 201);
    }

    public function myReviews(Request $request)
    {
        $user = $request->user();
        
        $reviews = Review::where('user_id', $user->id)
            ->with(['offering', 'booking'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'data' => $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'offering' => [
                        'id' => $review->offering->id,
                        'title' => $review->offering->title,
                    ],
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'photos' => $review->photos_json ?? [],
                    'status' => $review->status,
                    'created_at' => $review->created_at,
                ];
            })
        ]);
    }

    public function pendingReviews(Request $request)
    {
        $this->authorize('moderate', Review::class);
        
        $reviews = Review::where('status', 'pending')
            ->with(['user', 'offering', 'booking'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'data' => $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'booking_id' => $review->booking_id,
                    'user' => [
                        'id' => $review->user->id,
                        'name' => $review->user->name,
                        'email' => $review->user->email,
                    ],
                    'offering' => [
                        'id' => $review->offering->id,
                        'title' => $review->offering->title,
                    ],
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'photos' => $review->photos_json ?? [],
                    'created_at' => $review->created_at,
                ];
            })
        ]);
    }

    public function approve(Request $request, int $id)
    {
        $this->authorize('moderate', Review::class);
        
        $review = Review::findOrFail($id);
        $review->approve($request->user()->id);
        
        return response()->json(['message' => 'Review approved']);
    }

    public function reject(Request $request, int $id)
    {
        $this->authorize('moderate', Review::class);
        
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500']
        ]);
        
        $review = Review::findOrFail($id);
        $review->reject($data['reason'], $request->user()->id);
        
        return response()->json(['message' => 'Review rejected']);
    }
}
