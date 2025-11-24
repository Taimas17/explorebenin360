<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class PaystackWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function makeSuccessPayload(string $reference): array
    {
        return [
            'event' => 'charge.success',
            'data' => ['reference' => $reference],
        ];
    }

    private function makeFailedPayload(string $reference): array
    {
        return [
            'event' => 'charge.failed',
            'data' => ['reference' => $reference],
        ];
    }

    public function test_charge_success_is_idempotent_under_double_call(): void
    {
        Config::set('payments.paystack.secret_key', 'test_secret');
        $this->artisan('migrate');
        $this->seed();

        $user = User::factory()->create(['email' => 'buyer@example.com']);
        $user->assignRole('traveler');
        $offering = Offering::first();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(200)->json();
        $token = $login['token'];

        $session = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/checkout/session', [
                'offering_id' => $offering->id,
                'start_date' => now()->addDays(1)->toDateString(),
                'guests' => 2,
            ])->assertStatus(201)->json();

        $reference = $session['reference'];

        Mail::fake();

        $payload = json_encode($this->makeSuccessPayload($reference));
        $sig = hash_hmac('sha512', $payload, 'test_secret');

        $this->withHeader('x-paystack-signature', $sig)
            ->post('/api/v1/payments/paystack/webhook', [], [], [], [], $payload)
            ->assertStatus(200);

        $this->withHeader('x-paystack-signature', $sig)
            ->post('/api/v1/payments/paystack/webhook', [], [], [], [], $payload)
            ->assertStatus(200);

        $booking = Booking::where('payment_ref', $reference)->firstOrFail();
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals('success', $booking->payment_status);
        $this->assertNotNull($booking->commission_amount);
        $this->assertNotNull($booking->webhook_processed_at);

        Mail::assertQueued(BookingConfirmed::class, 1);
    }

    public function test_transaction_exception_returns_500_and_logs(): void
    {
        Config::set('payments.paystack.secret_key', 'test_secret');
        $this->artisan('migrate');
        $this->seed();

        $user = User::factory()->create();
        $offering = Offering::first();

        $booking = Booking::create([
            'user_id' => $user->id,
            'offering_id' => $offering->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'guests' => 1,
            'status' => 'pending',
            'amount' => 1000,
            'currency' => 'XOF',
            'payment_provider' => 'paystack',
            'payment_ref' => 'EB360-TST-'.time(),
        ]);

        $payload = json_encode($this->makeSuccessPayload($booking->payment_ref));
        $sig = hash_hmac('sha512', $payload, 'test_secret');

        Mail::fake();

        DB::shouldReceive('transaction')->andThrow(new \Exception('deadlock'));

        $this->withHeader('x-paystack-signature', $sig)
            ->post('/api/v1/payments/paystack/webhook', [], [], [], [], $payload)
            ->assertStatus(500)
            ->assertJson(['message' => 'Processing failed']);

        Mail::assertNothingQueued();
    }

    public function test_already_confirmed_status_is_ignored(): void
    {
        Config::set('payments.paystack.secret_key', 'test_secret');
        $this->artisan('migrate');
        $this->seed();

        $user = User::factory()->create();
        $offering = Offering::first();

        $booking = Booking::create([
            'user_id' => $user->id,
            'offering_id' => $offering->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'guests' => 1,
            'status' => 'confirmed',
            'amount' => 1000,
            'currency' => 'XOF',
            'payment_provider' => 'paystack',
            'payment_ref' => 'EB360-TST-'.time(),
            'payment_status' => 'success',
        ]);

        $payload = json_encode($this->makeSuccessPayload($booking->payment_ref));
        $sig = hash_hmac('sha512', $payload, 'test_secret');

        Mail::fake();

        $this->withHeader('x-paystack-signature', $sig)
            ->post('/api/v1/payments/paystack/webhook', [], [], [], [], $payload)
            ->assertStatus(200)
            ->assertJson(['message' => 'Already handled']);

        Mail::assertNothingQueued();
    }
}
