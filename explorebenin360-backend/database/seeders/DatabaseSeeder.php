<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Article;
use App\Models\Event;
use App\Models\Guide;
use App\Models\Offering;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Roles
        $roles = ['admin','provider','traveler'];
        foreach ($roles as $r) { Role::findOrCreate($r, 'web'); }

        // Admin
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        // Providers
        $providers = [];
        for ($i=1; $i<=3; $i++) {
            $p = User::factory()->create([
                'name' => "Provider $i",
                'email' => "provider$i@example.com",
            ]);
            $p->assignRole('provider');
            $providers[] = $p;
        }

        // Traveler sample
        $traveler = User::factory()->create([
            'name' => 'Traveler',
            'email' => 'traveler@example.com',
        ]);
        $traveler->assignRole('traveler');

        // Content seeds
        Place::factory(15)->create();
        Accommodation::factory(12)->create();
        Guide::factory(10)->create();
        Article::factory(20)->create();
        Event::factory(6)->create();

        // Offerings seeds (basic)
        $places = Place::inRandomOrder()->take(6)->get();
        foreach ($places as $idx => $pl) {
            Offering::create([
                'provider_id' => $providers[$idx % count($providers)]->id,
                'place_id' => $pl->id,
                'type' => 'experience',
                'title' => 'Experience at ' . $pl->title,
                'slug' => 'experience-' . $pl->slug,
                'description' => 'Guided visit at ' . $pl->title,
                'price' => rand(10000, 50000) / 100,
                'currency' => 'XOF',
                'capacity' => rand(5, 20),
                'status' => 'published',
            ]);
        }
    }
}
