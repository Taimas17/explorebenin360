<?php

namespace Tests\Feature;

use App\Models\Offering;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AuthBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_happy_path_checkout_and_webhook_confirms_booking(): void
    {
        Config::set('payments.paystack.secret_key', 'test_secret');
        $this->artisan('migrate');
        $this->seed();

        $user = User::factory()->create(['email' => 'buyer@example.com']);
        $user->assignRole('traveler');
        $offering = Offering::first();

        $register = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(200)->json();
        $token = $register['token'];

        $session = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/checkout/session', [
                'offering_id' => $offering->id,
                'start_date' => now()->addDays(1)->toDateString(),
                'guests' => 2,
            ])->assertStatus(201)->json();

        $reference = $session['reference'];

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => [ 'reference' => $reference ],
        ]);
        $signature = hash_hmac('sha512', $payload, 'test_secret');
        $this->withHeader('x-paystack-signature', $signature)
            ->post('/api/v1/payments/paystack/webhook', [], [], [], [], $payload)
            ->assertStatus(200);
    }
}
