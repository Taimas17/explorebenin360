<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'status' => ['nullable', 'in:pending,approved,rejected'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = $data['per_page'] ?? 20;

        $query = Review::with(['user:id,name,email', 'reviewable', 'booking'])
            ->orderByDesc('created_at');

        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }

        $reviews = $query->paginate($perPage);

        return response()->json([
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
            ]
        ]);
    }

    public function approve(Request $request, int $id)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $review = Review::findOrFail($id);
        $this->authorize('moderate', $review);

        $review->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);

        return response()->json(['data' => $review, 'message' => 'Review approved']);
    }

    public function reject(Request $request, int $id)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $review = Review::findOrFail($id);
        $this->authorize('moderate', $review);

        $review->update([
            'status' => 'rejected',
        ]);

        return response()->json(['data' => $review, 'message' => 'Review rejected']);
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $review = Review::findOrFail($id);
        $this->authorize('moderate', $review);

        $review->delete();

        return response()->json(['message' => 'Review deleted']);
    }
}
