<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccommodationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        Accommodation::factory(3)->create();
        $this->getJson('/api/v1/accommodations')->assertStatus(200)->assertJsonStructure(['data','meta']);
    }
}
