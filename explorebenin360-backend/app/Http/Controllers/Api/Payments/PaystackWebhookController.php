<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Services\PaystackClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('x-paystack-signature');
        if (!PaystackClient::verifySignature($payload, $signature)) {
            Log::warning('Invalid Paystack signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data', []);
        $reference = $data['reference'] ?? null;
        if (!$reference) return response()->json(['message' => 'No reference'], 400);

        $booking = Booking::where('payment_ref', $reference)->first();
        if (!$booking) return response()->json(['message' => 'Unknown reference'], 200);

        if ($event === 'charge.success') {
            if (in_array($booking->status, ['confirmed','refunded'])) {
                return response()->json(['message' => 'Already handled'], 200);
            }
            $booking->payment_status = 'success';
            $booking->status = 'confirmed';
            $pct = (float) config('payments.commission_percent', 12);
            $booking->commission_amount = round($booking->amount * ($pct/100), 2);
            $booking->save();

            Mail::to($booking->user->email)
                ->cc(optional($booking->offering->provider)->email)
                ->queue(new BookingConfirmed($booking));

            return response()->json(['message' => 'ok']);
        }

        if ($event === 'charge.failed') {
            if ($booking->status === 'pending') {
                $booking->payment_status = 'failed';
                $booking->status = 'cancelled';
                $booking->save();
            }
            return response()->json(['message' => 'ok']);
        }

        return response()->json(['message' => 'ignored']);
    }
}
