<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Notifications\NewReviewNotification;
use App\Notifications\ReviewResponseNotification;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'reviewable_type' => ['nullable','string','max:120'],
            'reviewable_id' => ['nullable','integer','min:1'],
            'verified_only' => ['nullable','boolean'],
            'sort' => ['nullable', Rule::in(['recent','rating','helpful'])],
            'page' => ['nullable','integer','min:1'],
        ]);

        $query = Review::query()->with([
            'user:id,name',
            'responder:id,name',
        ])->published();

        $avg = null; $total = null;

        if (!empty($data['reviewable_type']) && !empty($data['reviewable_id'])) {
            $query->where('reviewable_type', $data['reviewable_type'])
                  ->where('reviewable_id', $data['reviewable_id']);
            $avg = (float) Review::published()
                ->forReviewable($data['reviewable_type'], (int)$data['reviewable_id'])
                ->avg('rating');
            $total = (int) Review::published()
                ->forReviewable($data['reviewable_type'], (int)$data['reviewable_id'])
                ->count();
        }

        if (!empty($data['verified_only'])) {
            $query->verified();
        }

        $sort = $data['sort'] ?? 'recent';
        if ($sort === 'rating') $query->orderByDesc('rating');
        else if ($sort === 'helpful') $query->orderByDesc('helpful_count');
        else $query->orderByDesc('created_at');

        $paginator = $query->paginate(15)->appends($request->query());

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'average_rating' => $avg ?? 0,
                'total_reviews' => $total ?? 0,
            ],
        ]);
    }

    public function show(int $id)
    {
        $review = Review::with(['user','responder','reviewable','booking'])->findOrFail($id);
        $this->authorize('view', $review);
        return response()->json(['data' => $review]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Review::class);
        $data = $request->validate([
            'reviewable_type' => ['required', Rule::in(['App\\Models\\Accommodation','App\\Models\\Guide','App\\Models\\Offering','App\\Models\\Event'])],
            'reviewable_id' => ['required','integer','min:1'],
            'booking_id' => ['nullable','exists:bookings,id'],
            'rating' => ['required','integer','min:1','max:5'],
            'title' => ['nullable','string','max:255'],
            'body' => ['required','string','min:10','max:2000'],
        ]);

        $validator = validator($data, []);
        $validator->after(function($v) use ($data, $request) {
            $userId = $request->user()->id;
            $exists = Review::where('user_id', $userId)
                ->where('reviewable_type', $data['reviewable_type'])
                ->where('reviewable_id', $data['reviewable_id'])
                ->exists();
            if ($exists) {
                $v->errors()->add('review', 'Vous avez déjà laissé un avis.');
            }

            $class = $data['reviewable_type'];
            try {
                $model = $class::findOrFail($data['reviewable_id']);
            } catch (ModelNotFoundException $e) {
                $v->errors()->add('reviewable_id', 'Entité introuvable.');
            }

            if (!empty($data['booking_id'])) {
                $booking = Booking::find($data['booking_id']);
                if (!$booking || $booking->user_id !== $userId) {
                    $v->errors()->add('booking_id', 'Réservation invalide.');
                } elseif ($booking->status !== 'confirmed') {
                    $v->errors()->add('booking_id', 'La réservation doit être confirmée.');
                }
            }
        });
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $verified = false;
        if (!empty($data['booking_id'])) {
            $verified = true;
        }

        $review = new Review();
        $review->fill([
            'reviewable_type' => $data['reviewable_type'],
            'reviewable_id' => $data['reviewable_id'],
            'user_id' => $request->user()->id,
            'booking_id' => $data['booking_id'] ?? null,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
            'verified_purchase' => $verified,
            'status' => 'pending',
        ]);

        $autoApprove = (bool) (config('reviews.auto_approve_verified') ?? false);
        if ($autoApprove && $verified) {
            $review->status = 'published';
        }
        $review->save();

        $review->load('reviewable','user');
        $parent = $review->reviewable;
        if ($parent && method_exists($parent, 'updateAverageRating') && $review->status === 'published') {
            $parent->updateAverageRating();
        }

        if ($parent && method_exists($parent, 'provider')) {
            $provider = $parent->provider; if ($provider) { $provider->notify(new NewReviewNotification($review)); }
        }

        return response()->json(['data' => $review], 201);
    }

    public function update(Request $request, int $id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('update', $review);
        $data = $request->validate([
            'rating' => ['sometimes','integer','min:1','max:5'],
            'title' => ['sometimes','nullable','string','max:255'],
            'body' => ['sometimes','string','min:10','max:2000'],
        ]);
        $oldRating = $review->rating;
        $review->fill($data);
        $review->save();

        if ($review->status === 'published' && isset($data['rating']) && $review->rating !== $oldRating) {
            $parent = $review->reviewable;
            if ($parent && method_exists($parent, 'updateAverageRating')) {
                $parent->updateAverageRating();
            }
        }
        return response()->json(['data' => $review]);
    }

    public function destroy(Request $request, int $id)
    {
        $review = Review::with('reviewable')->findOrFail($id);
        $this->authorize('delete', $review);
        $parent = $review->reviewable;
        $review->delete();
        if ($parent && method_exists($parent, 'updateAverageRating')) {
            $parent->updateAverageRating();
        }
        return response()->json(['message' => 'Review deleted']);
    }

    public function respond(Request $request, int $id)
    {
        $review = Review::with('user')->findOrFail($id);
        $this->authorize('respond', $review);
        $data = $request->validate([
            'response' => ['required','string','max:1000'],
        ]);
        $review->response = $data['response'];
        $review->response_by = $request->user()->id;
        $review->response_at = now();
        $review->save();

        $review->user?->notify(new ReviewResponseNotification($review));

        return response()->json(['data' => $review]);
    }

    public function helpful(Request $request, int $id)
    {
        $request->validate([]); // auth handled by middleware
        $review = Review::findOrFail($id);
        $review->increment('helpful_count');
        $review->refresh();
        return response()->json(['helpful_count' => $review->helpful_count]);
    }
}
