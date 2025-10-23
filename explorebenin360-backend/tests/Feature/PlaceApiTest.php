<?php

namespace Tests\Feature;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_places_index_returns_paginated_results(): void
    {
        Place::factory(5)->create();
        $res = $this->getJson('/api/v1/places');
        $res->assertStatus(200)->assertJsonStructure(['data','meta']);
    }

    public function test_places_show_by_slug(): void
    {
        $place = Place::factory()->create();
        $res = $this->getJson('/api/v1/places/'.$place->slug);
        $res->assertStatus(200)->assertJsonPath('data.slug', $place->slug);
    }

    public function test_places_filter_by_city(): void
    {
        Place::factory()->create(['city' => 'Cotonou']);
        Place::factory()->create(['city' => 'Parakou']);
        $res = $this->getJson('/api/v1/places?city=Cotonou');
        $res->assertStatus(200)->assertJsonCount(1, 'data');
    }
}
