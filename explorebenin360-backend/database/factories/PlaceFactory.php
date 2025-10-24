<?php

namespace Database\Factories;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlaceFactory extends Factory
{
    protected $model = Place::class;

    public function definition(): array
    {
        $types = ['city','site','museum','park','beach','culture','history','gastronomy','adventure','other'];
        $title = $this->faker->unique()->city() . ' ' . $this->faker->word();
        $lat = $this->faker->randomFloat(7, 6.2, 12.5);
        $lng = $this->faker->randomFloat(7, 0.8, 3.9);
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'type' => $this->faker->randomElement($types),
            'description' => '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>',
            'city' => $this->faker->randomElement(['Cotonou','Porto-Novo','Parakou','Ouidah','Abomey','Bohicon','Natitingou','Grand-Popo']),
            'country' => 'Benin',
            'lat' => $lat,
            'lng' => $lng,
            'price_from' => $this->faker->boolean(70) ? $this->faker->randomFloat(2, 0, 500) : null,
            'opening_hours_json' => $this->faker->boolean(40) ? ['mon'=>'9:00-18:00','tue'=>'9:00-18:00','wed'=>'9:00-18:00','thu'=>'9:00-18:00','fri'=>'9:00-18:00','sat'=>'10:00-16:00','sun'=>'closed'] : null,
            'tags' => $this->faker->randomElements(['famille','nature','culture','histoire','plage','aventure','gastronomie'], $this->faker->numberBetween(1,3)),
            'cover_image_url' => null,
            'rating_avg' => $this->faker->randomFloat(2, 3.5, 5),
            'review_count' => $this->faker->numberBetween(0, 450),
            'featured' => $this->faker->boolean(20),
            'status' => 'published',
        ];
    }
}
