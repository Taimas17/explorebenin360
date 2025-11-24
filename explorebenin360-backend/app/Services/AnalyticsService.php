<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    private function parsePeriod(string $period): array
    {
        return match($period) {
            '7d' => [now()->subDays(7), now()],
            '30d' => [now()->subDays(30), now()],
            '90d' => [now()->subDays(90), now()],
            '1y' => [now()->subYear(), now()],
            'all' => [null, null],
            default => [now()->subDays(30), now()],
        };
    }

    public function calculateUserKPIs(string $period, bool $compare = false): array
    {
        [$start, $end] = $this->parsePeriod($period);
        
        $query = DB::table('users');
        
        $total_users = (clone $query)->count();
        
        $new_users = $start && $end 
            ? (clone $query)->whereBetween('created_at', [$start, $end])->count()
            : 0;
        
        $active_users = $start && $end
            ? (clone $query)->whereBetween('last_login_at', [$start, $end])->count()
            : (clone $query)->whereNotNull('last_login_at')->count();
        
        $growth_rate = 0;
        if ($compare && $start && $end) {
            $duration = $start->diffInDays($end);
            $previousStart = (clone $start)->subDays($duration);
            $previousEnd = clone $start;
            $previous_new_users = (clone $query)->whereBetween('created_at', [$previousStart, $previousEnd])->count();
            $growth_rate = $previous_new_users > 0 
                ? (($new_users - $previous_new_users) / $previous_new_users) * 100 
                : 0;
        }
        
        return [
            'total' => $total_users,
            'new' => $new_users,
            'active' => $active_users,
            'growth' => round($growth_rate, 1),
        ];
    }

    public function calculateProviderKPIs(): array
    {
        $usersTable = DB::table('users');
        
        $total_providers = (clone $usersTable)->where('provider_status', 'approved')->count();
        $pending_providers = (clone $usersTable)->where('provider_status', 'pending')->count();
        $approved_providers = $total_providers;
        
        // Active providers = ceux avec au moins 1 offering publié
        $active_providers = DB::table('offerings')
            ->select('provider_id')
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->distinct()
            ->count();
        
        return [
            'total' => $total_providers,
            'pending' => $pending_providers,
            'approved' => $approved_providers,
            'active' => $active_providers,
        ];
    }

    public function calculateContentKPIs(): array
    {
        return [
            'accommodations' => DB::table('accommodations')->count(),
            'accommodations_published' => DB::table('accommodations')->where('status', 'published')->count(),
            'articles' => DB::table('articles')->count(),
            'articles_published' => DB::table('articles')->where('status', 'published')->count(),
            'events' => DB::table('events')->count(),
            'events_published' => DB::table('events')->where('status', 'published')->count(),
            'guides' => DB::table('guides')->count(),
            'guides_published' => DB::table('guides')->where('status', 'published')->count(),
            'places' => DB::table('places')->count(),
            'places_published' => DB::table('places')->where('status', 'published')->count(),
        ];
    }

    public function calculateBookingKPIs(string $period): array
    {
        [$start, $end] = $this->parsePeriod($period);
        
        $query = DB::table('bookings');
        
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }
        
        $stats = $query->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
        ')->first();
        
        $conversion_rate = ($stats->confirmed + $stats->cancelled) > 0
            ? ($stats->confirmed / ($stats->confirmed + $stats->cancelled)) * 100
            : 0;
        
        return [
            'total' => $stats->total ?? 0,
            'confirmed' => $stats->confirmed ?? 0,
            'pending' => $stats->pending ?? 0,
            'cancelled' => $stats->cancelled ?? 0,
            'conversion_rate' => round($conversion_rate, 1),
        ];
    }

    public function calculateRevenueKPIs(string $period): array
    {
        [$start, $end] = $this->parsePeriod($period);
        
        $query = DB::table('bookings')->where('status', 'confirmed');
        
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }
        
        $stats = $query->selectRaw('
            COALESCE(SUM(amount), 0) as total_revenue,
            COALESCE(SUM(commission_amount), 0) as total_commission,
            COALESCE(AVG(amount), 0) as average,
            currency
        ')->groupBy('currency')->get();
        
        // Si plusieurs devises, on prend XOF par défaut ou la première
        $mainStats = $stats->where('currency', 'XOF')->first() ?? $stats->first();
        
        return [
            'total' => (float) ($mainStats->total_revenue ?? 0),
            'commission' => (float) ($mainStats->total_commission ?? 0),
            'average' => (float) ($mainStats->average ?? 0),
            'currency' => $mainStats->currency ?? 'XOF',
        ];
    }

    public function calculateEngagementKPIs(): array
    {
        $total_favorites = DB::table('favorites')->count();
        
        // Pour reviews, si la table existe (sinon mettre 0)
        $total_reviews = 0;
        $average_rating = 0.0;
        
        return [
            'favorites' => $total_favorites,
            'reviews' => $total_reviews,
            'average_rating' => $average_rating,
        ];
    }

    public function generateTimeseries(string $metric, string $period, string $granularity): array
    {
        [$start, $end] = $this->parsePeriod($period);
        
        if (!$start || !$end) {
            return [];
        }
        
        $dateFormat = match($granularity) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%W',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };
        
        $series = [];
        
        switch ($metric) {
            case 'users':
                $data = DB::table('users')
                    ->selectRaw("DATE_FORMAT(created_at, '$dateFormat') as date, COUNT(*) as value")
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                $series = $data->toArray();
                break;
                
            case 'bookings':
                $data = DB::table('bookings')
                    ->selectRaw("DATE_FORMAT(created_at, '$dateFormat') as date, COUNT(*) as value")
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                $series = $data->toArray();
                break;
                
            case 'revenue':
                $data = DB::table('bookings')
                    ->selectRaw("DATE_FORMAT(created_at, '$dateFormat') as date, COALESCE(SUM(amount), 0) as value")
                    ->where('status', 'confirmed')
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                $series = $data->toArray();
                break;
                
            case 'favorites':
                $data = DB::table('favorites')
                    ->selectRaw("DATE_FORMAT(created_at, '$dateFormat') as date, COUNT(*) as value")
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                $series = $data->toArray();
                break;
        }
        
        return array_map(function($item) {
            return [
                'date' => $item->date,
                'value' => (int) $item->value,
            ];
        }, $series);
    }

    public function getTopContent(string $type, string $metric, int $limit, string $period): array
    {
        [$start, $end] = $this->parsePeriod($period);
        
        $table = match($type) {
            'accommodations' => 'accommodations',
            'guides' => 'guides',
            'offerings' => 'offerings',
            'articles' => 'articles',
            default => 'accommodations',
        };
        
        if ($metric === 'bookings' && in_array($type, ['accommodations', 'guides', 'offerings'])) {
            // Top par nombre de bookings
            $query = DB::table('bookings')
                ->join('offerings', 'bookings.offering_id', '=', 'offerings.id')
                ->join($table, 'offerings.id', '=', "$table.id")
                ->where('bookings.status', 'confirmed')
                ->select([
                    "$table.id",
                    "$table.title",
                    "$table.slug",
                    "$table.cover_image_url",
                    DB::raw('COUNT(bookings.id) as metric_value')
                ])
                ->groupBy("$table.id", "$table.title", "$table.slug", "$table.cover_image_url")
                ->orderByDesc('metric_value')
                ->limit($limit);
            
            if ($start && $end) {
                $query->whereBetween('bookings.created_at', [$start, $end]);
            }
            
            return $query->get()->toArray();
        }
        
        if ($metric === 'favorites') {
            // Top par nombre de favoris
            $query = DB::table('favorites')
                ->join($table, function($join) use ($table) {
                    $join->on('favorites.item_id', '=', "$table.id")
                         ->where('favorites.type', '=', $table);
                })
                ->select([
                    "$table.id",
                    "$table.title",
                    "$table.slug",
                    "$table.cover_image_url",
                    DB::raw('COUNT(favorites.id) as metric_value')
                ])
                ->groupBy("$table.id", "$table.title", "$table.slug", "$table.cover_image_url")
                ->orderByDesc('metric_value')
                ->limit($limit);
            
            if ($start && $end) {
                $query->whereBetween('favorites.created_at', [$start, $end]);
            }
            
            return $query->get()->toArray();
        }
        
        // Par défaut, retourner les plus récents
        $query = DB::table($table)
            ->select(['id', 'title', 'slug', 'cover_image_url', DB::raw('1 as metric_value')])
            ->orderByDesc('created_at')
            ->limit($limit);
        
        return $query->get()->toArray();
    }
}
