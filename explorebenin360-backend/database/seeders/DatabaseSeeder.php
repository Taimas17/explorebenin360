<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Article;
use App\Models\Event;
use App\Models\Guide;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Place::factory(15)->create();
        Accommodation::factory(12)->create();
        Guide::factory(10)->create();
        Article::factory(20)->create();
        Event::factory(6)->create();
    }
}
