<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use Illuminate\Http\Request;

class AdminPayoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'status' => ['nullable', 'in:pending,processing,completed,failed,cancelled'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $perPage = $data['per_page'] ?? 20;
        $query = Payout::with(['provider:id,name,email,business_name', 'paymentMethod', 'processedBy'])
            ->orderByDesc('requested_at');
        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }
        $payouts = $query->paginate($perPage);
        return response()->json([
            'data' => $payouts->items(),
            'meta' => [
                'current_page' => $payouts->currentPage(),
                'per_page' => $payouts->perPage(),
                'total' => $payouts->total(),
                'last_page' => $payouts->lastPage(),
            ]
        ]);
    }

    public function process(Request $request, int $id)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $payout = Payout::findOrFail($id);
        $this->authorize('process', $payout);
        $data = $request->validate([
            'transaction_ref' => ['nullable', 'string', 'max:191'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $payout->update([
            'status' => 'processing',
            'transaction_ref' => $data['transaction_ref'] ?? null,
            'admin_notes' => $data['admin_notes'] ?? null,
            'processed_at' => now(),
            'processed_by' => $user->id,
        ]);
        return response()->json(['data' => $payout, 'message' => 'Payout processing started']);
    }

    public function complete(Request $request, int $id)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $payout = Payout::findOrFail($id);
        if (!in_array($payout->status, ['pending', 'processing'])) {
            return response()->json(['message' => 'Payout cannot be completed in current status'], 422);
        }
        $data = $request->validate([
            'transaction_ref' => ['nullable', 'string', 'max:191'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $payout->update([
            'status' => 'completed',
            'transaction_ref' => $data['transaction_ref'] ?? $payout->transaction_ref,
            'admin_notes' => $data['admin_notes'] ?? $payout->admin_notes,
            'completed_at' => now(),
            'processed_by' => $user->id,
        ]);
        return response()->json(['data' => $payout, 'message' => 'Payout completed']);
    }

    public function fail(Request $request, int $id)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $payout = Payout::findOrFail($id);
        $data = $request->validate([
            'failure_reason' => ['required', 'string', 'max:1000'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $payout->update([
            'status' => 'failed',
            'failure_reason' => $data['failure_reason'],
            'admin_notes' => $data['admin_notes'] ?? $payout->admin_notes,
            'processed_by' => $user->id,
        ]);
        return response()->json(['data' => $payout, 'message' => 'Payout marked as failed']);
    }
}
