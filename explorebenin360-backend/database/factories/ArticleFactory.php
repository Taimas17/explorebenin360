<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(6);
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'excerpt' => $this->faker->paragraph(3),
            'body' => '<p>' . implode('</p><p>', $this->faker->paragraphs(6)) . '</p>',
            'author_name' => $this->faker->name(),
            'category' => $this->faker->randomElement(['Conseils','Destinations','Culture','Actualités']),
            'tags' => $this->faker->randomElements(['conseil','culture','histoire','gastronomie','plage','aventure'], $this->faker->numberBetween(1,3)),
            'cover_image_url' => null,
            'status' => 'published',
            'published_at' => now()->subDays($this->faker->numberBetween(0, 180)),
        ];
    }
}
