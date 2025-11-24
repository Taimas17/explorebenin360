<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'status' => ['nullable','in:pending,published,rejected'],
            'reviewable_type' => ['nullable','string','max:120'],
            'verified_only' => ['nullable','boolean'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);
        $query = Review::with(['user:id,name','responder:id,name','reviewable'])
            ->orderByDesc('id');
        if (!empty($data['status'])) $query->where('status', $data['status']);
        if (!empty($data['reviewable_type'])) $query->where('reviewable_type', $data['reviewable_type']);
        if (!empty($data['verified_only'])) $query->where('verified_purchase', true);

        $perPage = (int) ($data['per_page'] ?? 20);
        return response()->json(['data' => $query->paginate($perPage)]);
    }

    public function approve(Request $request, int $id)
    {
        $review = Review::with('reviewable','user')->findOrFail($id);
        $this->authorize('moderate', $review);
        $review->status = 'published';
        $review->moderated_by = $request->user()->id;
        $review->moderated_at = now();
        $review->save();
        $parent = $review->reviewable;
        if ($parent && method_exists($parent, 'updateAverageRating')) {
            $parent->updateAverageRating();
        }
        $review->user?->notify(new \App\Notifications\ReviewApprovedNotification($review));
        return response()->json(['message' => 'Review approved']);
    }

    public function reject(Request $request, int $id)
    {
        $review = Review::with('user')->findOrFail($id);
        $this->authorize('moderate', $review);
        $data = $request->validate([
            'reason' => ['required','string','max:500'],
        ]);
        $review->status = 'rejected';
        $review->moderation_reason = $data['reason'];
        $review->moderated_by = $request->user()->id;
        $review->moderated_at = now();
        $review->save();
        $review->user?->notify(new \App\Notifications\ReviewRejectedNotification($review));
        return response()->json(['message' => 'Review rejected']);
    }
}
