<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Services\PaystackClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

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

        try {
            if ($event === 'charge.success') {
                $result = DB::transaction(function () use ($reference) {
                    $booking = Booking::where('payment_ref', $reference)
                        ->lockForUpdate()
                        ->first();

                    if (!$booking) {
                        return ['status' => 'unknown', 'booking' => null];
                    }

                    if (in_array($booking->status, ['confirmed','refunded'])) {
                        return ['status' => 'already', 'booking' => $booking];
                    }

                    $booking->payment_status = 'success';
                    $booking->status = 'confirmed';
                    $pct = (float) config('payments.commission_percent', 12);
                    $booking->commission_amount = round($booking->amount * ($pct/100), 2);
                    $booking->webhook_processed_at = now();
                    $booking->save();

                    return ['status' => 'updated', 'booking' => $booking->fresh(['user','offering.provider'])];
                }, 5);

                if ($result['status'] === 'unknown') {
                    return response()->json(['message' => 'Unknown reference'], 200);
                }
                if ($result['status'] === 'already') {
                    return response()->json(['message' => 'Already handled'], 200);
                }

                $booking = $result['booking'];
                Mail::to($booking->user->email)
                    ->cc(optional($booking->offering->provider)->email)
                    ->queue(new BookingConfirmed($booking));

                return response()->json(['message' => 'ok']);
            }

            if ($event === 'charge.failed') {
                $result = DB::transaction(function () use ($reference) {
                    $booking = Booking::where('payment_ref', $reference)
                        ->lockForUpdate()
                        ->first();

                    if (!$booking) {
                        return ['status' => 'unknown', 'booking' => null];
                    }

                    if ($booking->status === 'pending') {
                        $booking->payment_status = 'failed';
                        $booking->status = 'cancelled';
                        $booking->webhook_processed_at = now();
                        $booking->save();
                    }

                    return ['status' => 'handled', 'booking' => $booking];
                }, 5);

                if ($result['status'] === 'unknown') {
                    return response()->json(['message' => 'Unknown reference'], 200);
                }

                return response()->json(['message' => 'ok']);
            }
        } catch (\Throwable $e) {
            Log::error('Webhook processing failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Processing failed'], 500);
        }

        return response()->json(['message' => 'ignored']);
    }
}
