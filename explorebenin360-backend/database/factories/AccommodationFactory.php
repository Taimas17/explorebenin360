<?php

namespace Database\Factories;

use App\Models\Accommodation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AccommodationFactory extends Factory
{
    protected $model = Accommodation::class;

    public function definition(): array
    {
        $types = ['hotel','guesthouse','ecolodge','bnb','other'];
        $title = $this->faker->unique()->company() . ' ' . $this->faker->randomElement(['Hotel','Lodge','Maison','Guesthouse']);
        $lat = $this->faker->randomFloat(7, 6.2, 12.5);
        $lng = $this->faker->randomFloat(7, 0.8, 3.9);
        return [
            'provider_id' => null,
            'place_id' => null,
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'type' => $this->faker->randomElement($types),
            'description' => $this->faker->paragraphs(3, true),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->randomElement(['Cotonou','Porto-Novo','Parakou','Ouidah','Abomey','Bohicon','Natitingou','Grand-Popo']),
            'lat' => $lat,
            'lng' => $lng,
            'price_per_night' => $this->faker->randomFloat(2, 8000, 150000),
            'currency' => 'XOF',
            'amenities_json' => $this->faker->randomElements(['wifi','parking','pool','breakfast','aircon','restaurant'], $this->faker->numberBetween(2,5)),
            'capacity' => $this->faker->numberBetween(2, 6),
            'rating_avg' => $this->faker->randomFloat(2, 3.2, 5),
            'review_count' => $this->faker->numberBetween(0, 300),
            'featured' => $this->faker->boolean(20),
            'status' => 'published',
            'cover_image_url' => 'https://picsum.photos/seed/' . Str::random(6) . '/800/600',
        ];
    }
}
