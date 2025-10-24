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

        // Content seeds (≥20 each)
        $places = Place::factory(28)->create();
        $accommodations = Accommodation::factory(24)->create();
        $guides = Guide::factory(22)->create();
        $articles = Article::factory(26)->create();
        $events = Event::factory(20)->create();

        // Attach relations: map accommodations/events to nearest places by city when possible
        $placesByCity = $places->groupBy('city');
        $accommodations->each(function ($a) use ($placesByCity) {
            $pl = optional($placesByCity[$a->city] ?? null)->first();
            if ($pl) $a->update(['place_id' => $pl->id]);
        });
        $events->each(function ($e) use ($placesByCity) {
            $pl = optional($placesByCity[$e->city] ?? null)->first();
            if ($pl) $e->update(['place_id' => $pl->id]);
        });

        // Offerings seeds (experience + guide_service + accommodation)
        $offerings = collect();
        $pickPlace = fn() => $places->random();
        for ($i=0; $i<30; $i++) {
            $pl = $pickPlace();
            $type = $i % 5 === 0 ? 'accommodation' : ($i % 3 === 0 ? 'guide_service' : 'experience');
            $titlePrefix = $type === 'accommodation' ? 'Séjour' : ($type === 'guide_service' ? 'Guide' : 'Expérience');
            $slugPrefix = $type === 'accommodation' ? 'sejour' : ($type === 'guide_service' ? 'guide' : 'experience');
            $off = Offering::create([
                'provider_id' => $providers[$i % count($providers)]->id,
                'place_id' => $pl->id,
                'type' => $type,
                'title' => $titlePrefix . ' — ' . $pl->title,
                'slug' => $slugPrefix . '-' . $pl->slug . '-' . substr(uniqid('', true), -6),
                'description' => 'Découverte de ' . $pl->title . ' avec un expert local.',
                'price' => rand(1500, 150000) / 1.0,
                'currency' => 'XOF',
                'capacity' => rand(2, 20),
                'status' => 'published',
            ]);
            $offerings->push($off);
        }

        // Bookings seeds
        $statuses = ['pending','authorized','confirmed','cancelled'];
        for ($i=0; $i<36; $i++) {
            $off = $offerings->random();
            $start = now()->addDays(rand(-20, 40));
            $end = (clone $start)->addDays(rand(0, 5));
            $amount = $off->price * rand(1, 3);
            $travelerId = $traveler->id;
            \App\Models\Booking::create([
                'user_id' => $travelerId,
                'offering_id' => $off->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'guests' => rand(1, 5),
                'status' => $statuses[array_rand($statuses)],
                'amount' => $amount,
                'currency' => 'XOF',
                'commission_amount' => round($amount * 0.12, 2),
                'payment_provider' => 'paystack',
                'payment_ref' => null,
                'payment_status' => null,
            ]);
        }
    }
}
