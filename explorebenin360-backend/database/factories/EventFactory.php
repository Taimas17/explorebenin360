<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = 'Événement ' . $this->faker->unique()->word();
        $start = now()->addDays($this->faker->numberBetween(-30, 60))->startOfDay();
        $end = (clone $start)->addDays($this->faker->numberBetween(0, 3));
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'place_id' => null,
            'city' => $this->faker->randomElement(['Cotonou','Porto-Novo','Parakou','Ouidah','Abomey','Bohicon','Natitingou','Grand-Popo']),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'organizer_name' => $this->faker->boolean(70) ? $this->faker->company() : null,
            'organizer_contact' => $this->faker->boolean(50) ? $this->faker->phoneNumber() : null,
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->boolean(60) ? $this->faker->randomFloat(2, 0, 50000) : null,
            'currency' => 'XOF',
            'category' => $this->faker->randomElement(['Festival','Culture','Musique','Sport','Marché']),
            'cover_image_url' => 'https://picsum.photos/seed/' . Str::random(6) . '/800/600',
            'status' => 'published',
            'featured' => $this->faker->boolean(20),
        ];
    }
}
