<?php

namespace Database\Factories;

use App\Models\Guide;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GuideFactory extends Factory
{
    protected $model = Guide::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        $langs = ['fr','en','yo','fon','bariba','dendi'];
        $specs = ['culture','histoire','nature','gastronomie','plage','aventure','oiseaux'];
        return [
            'user_id' => null,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(6),
            'languages_json' => $this->faker->randomElements($langs, $this->faker->numberBetween(1,3)),
            'specialties_json' => $this->faker->randomElements($specs, $this->faker->numberBetween(1,3)),
            'bio' => $this->faker->paragraphs(2, true),
            'avatar_url' => 'https://i.pravatar.cc/300?u=' . Str::random(6),
            'city' => $this->faker->randomElement(['Cotonou','Porto-Novo','Parakou','Ouidah','Abomey','Bohicon','Natitingou','Grand-Popo']),
            'lat' => $this->faker->boolean(60) ? $this->faker->randomFloat(7, 6.2, 12.5) : null,
            'lng' => $this->faker->boolean(60) ? $this->faker->randomFloat(7, 0.8, 3.9) : null,
            'price_per_day' => $this->faker->boolean(80) ? $this->faker->randomFloat(2, 15000, 120000) : null,
            'currency' => 'XOF',
            'verified' => $this->faker->boolean(30),
            'rating_avg' => $this->faker->randomFloat(2, 3, 5),
            'review_count' => $this->faker->numberBetween(0, 200),
            'status' => 'published',
        ];
    }
}
