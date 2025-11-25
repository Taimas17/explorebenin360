<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->isProvider()) {
            return response()->json(['message' => 'Provider account required'], 403);
        }
        $data = $request->validate([
            'status' => ['nullable', 'in:pending,processing,completed,failed,cancelled'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);
        $perPage = $data['per_page'] ?? 20;
        $query = Payout::where('provider_id', $user->id)
            ->with(['paymentMethod', 'processedBy:id,name'])
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

    public function show(Request $request, int $id)
    {
        $payout = Payout::with(['paymentMethod', 'processedBy'])->findOrFail($id);
        $this->authorize('view', $payout);
        return response()->json(['data' => $payout]);
    }

    public function balance(Request $request)
    {
        $user = $request->user();
        if (!$user->isProvider()) {
            return response()->json(['message' => 'Provider account required'], 403);
        }
        $earnings = DB::table('bookings')
            ->join('offerings', 'bookings.offering_id', '=', 'offerings.id')
            ->where('offerings.provider_id', $user->id)
            ->where('bookings.status', 'confirmed')
            ->selectRaw('
                COALESCE(SUM(bookings.amount), 0) as total_revenue,
                COALESCE(SUM(bookings.commission_amount), 0) as total_commission
            ')
            ->first();
        $totalEarnings = ($earnings->total_revenue ?? 0) - ($earnings->total_commission ?? 0);
        $totalPayouts = Payout::where('provider_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');
        $pendingPayouts = Payout::where('provider_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');
        $availableBalance = $totalEarnings - $totalPayouts - $pendingPayouts;
        return response()->json([
            'data' => [
                'total_earnings' => (float) $totalEarnings,
                'total_payouts' => (float) $totalPayouts,
                'pending_payouts' => (float) $pendingPayouts,
                'available_balance' => (float) $availableBalance,
                'currency' => 'XOF',
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $this->authorize('create', Payout::class);
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
        ]);
        $balanceData = $this->balance($request)->getData()->data;
        $availableBalance = $balanceData->available_balance;
        if ($data['amount'] > $availableBalance) {
            return response()->json([
                'message' => 'Insufficient balance',
                'available_balance' => $availableBalance,
            ], 422);
        }
        $paymentMethodId = $data['payment_method_id'] ?? null;
        if (!$paymentMethodId) {
            $defaultMethod = PaymentMethod::where('user_id', $user->id)
                ->where('is_default', true)
                ->first();
            if (!$defaultMethod) {
                return response()->json([
                    'message' => 'No default payment method found. Please add a payment method first.',
                ], 422);
            }
            $paymentMethodId = $defaultMethod->id;
        } else {
            $method = PaymentMethod::findOrFail($paymentMethodId);
            if ($method->user_id !== $user->id) {
                return response()->json(['message' => 'Payment method does not belong to you'], 403);
            }
        }
        $payout = Payout::create([
            'provider_id' => $user->id,
            'payment_method_id' => $paymentMethodId,
            'amount' => $data['amount'],
            'currency' => 'XOF',
            'status' => 'pending',
            'requested_at' => now(),
        ]);
        $payout->reference = Payout::generateReference($payout->id);
        $payout->save();
        return response()->json(['data' => $payout->load('paymentMethod')], 201);
    }

    public function cancel(Request $request, int $id)
    {
        $payout = Payout::findOrFail($id);
        $this->authorize('cancel', $payout);
        $payout->update(['status' => 'cancelled']);
        return response()->json(['data' => $payout, 'message' => 'Payout cancelled']);
    }
}
