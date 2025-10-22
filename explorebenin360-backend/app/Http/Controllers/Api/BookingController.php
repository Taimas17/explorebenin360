<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\Offering;
use App\Services\PaystackClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function createCheckoutSession(Request $request, PaystackClient $paystack)
    {
        $data = $request->validate([
            'offering_id' => ['required','exists:offerings,id'],
            'start_date' => ['required','date','after_or_equal:today'],
            'end_date' => ['nullable','date','after_or_equal:start_date'],
            'guests' => ['nullable','integer','min:1'],
        ]);
        $user = $request->user();
        $offering = Offering::where('status','published')->findOrFail($data['offering_id']);
        $guests = (int)($data['guests'] ?? 1);
        if ($guests > $offering->capacity) {
            return response()->json(['message' => 'Capacity exceeded'], 422);
        }

        $start = Carbon::parse($data['start_date']);
        $end = !empty($data['end_date']) ? Carbon::parse($data['end_date']) : null;
        $days = $end ? $start->diffInDays($end) + 1 : 1;
        $amount = $offering->price * $days * $guests;

        $booking = Booking::create([
            'user_id' => $user->id,
            'offering_id' => $offering->id,
            'start_date' => $start->toDateString(),
            'end_date' => $end?->toDateString(),
            'guests' => $guests,
            'amount' => $amount,
            'currency' => $offering->currency,
            'status' => 'pending',
            'payment_provider' => 'paystack',
        ]);

        $reference = 'EB360-' . $booking->id . '-' . time();
        $callbackUrl = config('payments.paystack.callback_url');
        $payload = [
            'amount' => (int) round($amount * 100),
            'email' => $user->email,
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'currency' => $offering->currency,
            'metadata' => [
                'booking_id' => $booking->id,
                'offering_id' => $offering->id,
            ],
        ];

        $tx = $paystack->initializeTransaction($payload);
        $booking->payment_ref = $reference;
        $booking->payment_status = 'initialized';
        $booking->save();

        return response()->json([
            'booking_id' => $booking->id,
            'authorization_url' => $tx['authorization_url'] ?? null,
            'reference' => $reference,
        ], 201);
    }

    public function myIndex(Request $request)
    {
        $user = $request->user();
        $query = Booking::with('offering')->where('user_id', $user->id)->orderByDesc('id');
        return response()->json(['data' => $query->paginate(20)]);
    }

    public function show(Request $request, int $id)
    {
        $booking = Booking::with('offering','offering.provider')->findOrFail($id);
        $user = $request->user();
        if (!$user->hasRole('admin') && $booking->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json(['data' => $booking]);
    }

    public function cancel(Request $request, int $id)
    {
        $booking = Booking::with('offering','offering.provider')->findOrFail($id);
        $user = $request->user();
        if (!$user->hasRole('admin') && $booking->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        if (!in_array($booking->status, ['pending','authorized'])) {
            return response()->json(['message' => 'Cannot cancel at this stage'], 422);
        }
        $booking->status = 'cancelled';
        $booking->cancel_reason = 'Cancelled by user';
        $booking->save();

        Mail::to($booking->user->email)
            ->cc(optional($booking->offering->provider)->email)
            ->queue(new BookingCancelled($booking));

        return response()->json(['data' => $booking]);
    }

    public function providerIndex(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('provider') && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $query = Booking::with('offering')
            ->whereHas('offering', fn($q)=>$q->where('provider_id', $user->id))
            ->orderByDesc('id');
        return response()->json(['data' => $query->paginate(20)]);
    }

    public function adminIndex(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'status' => ['nullable','in:pending,authorized,confirmed,cancelled,refunded'],
            'from' => ['nullable','date'],
            'to' => ['nullable','date'],
        ]);
        $query = Booking::with('offering','user')->orderByDesc('id');
        if (!empty($data['status'])) $query->where('status',$data['status']);
        if (!empty($data['from'])) $query->whereDate('created_at','>=',$data['from']);
        if (!empty($data['to'])) $query->whereDate('created_at','<=',$data['to']);
        return response()->json(['data' => $query->paginate(20)]);
    }

    public function adminUpdate(Request $request, int $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $booking = Booking::findOrFail($id);
        $data = $request->validate([
            'status' => ['required','in:pending,authorized,confirmed,cancelled,refunded'],
        ]);
        $booking->status = $data['status'];
        if ($booking->status === 'confirmed') {
            $pct = (float) config('payments.commission_percent', 12);
            $booking->commission_amount = round($booking->amount * ($pct/100), 2);
        }
        $booking->save();
        return response()->json(['data' => $booking]);
    }
}
