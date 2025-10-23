<?php

namespace Tests\Unit;

use App\Services\PaystackClient;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PaystackSignatureTest extends TestCase
{
    public function test_signature_verification(): void
    {
        Config::set('payments.paystack.secret_key', 'secret');
        $payload = '{"hello":"world"}';
        $sig = hash_hmac('sha512', $payload, 'secret');
        $this->assertTrue(PaystackClient::verifySignature($payload, $sig));
        $this->assertFalse(PaystackClient::verifySignature($payload, 'invalid'));
    }
}
