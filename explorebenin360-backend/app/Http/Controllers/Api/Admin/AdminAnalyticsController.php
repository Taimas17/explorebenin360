<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminAnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    public function overview(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
        }
        
        $data = $request->validate([
            'period' => ['nullable', 'in:7d,30d,90d,1y,all'],
            'compare_previous' => ['nullable', 'boolean'],
        ]);
        
        $period = $data['period'] ?? '30d';
        $comparePrevious = $data['compare_previous'] ?? false;
        
        // Cache pour 5 minutes
        $cacheKey = "admin_analytics_overview_{$period}_" . ($comparePrevious ? '1' : '0');
        
        $kpis = Cache::remember($cacheKey, 300, function() use ($period, $comparePrevious) {
            return [
                'users' => $this->analyticsService->calculateUserKPIs($period, $comparePrevious),
                'providers' => $this->analyticsService->calculateProviderKPIs(),
                'content' => $this->analyticsService->calculateContentKPIs(),
                'bookings' => $this->analyticsService->calculateBookingKPIs($period),
                'revenue' => $this->analyticsService->calculateRevenueKPIs($period),
                'engagement' => $this->analyticsService->calculateEngagementKPIs(),
            ];
        });
        
        [$start, $end] = $this->parsePeriodForResponse($period);
        
        return response()->json([
            'data' => [
                'period' => $period,
                'start_date' => $start,
                'end_date' => $end,
                'kpis' => $kpis,
            ]
        ]);
    }

    public function timeseries(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
        }
        
        $data = $request->validate([
            'metric' => ['required', 'in:users,bookings,revenue,favorites'],
            'period' => ['nullable', 'in:7d,30d,90d,1y'],
            'granularity' => ['nullable', 'in:day,week,month'],
        ]);
        
        $metric = $data['metric'];
        $period = $data['period'] ?? '30d';
        $granularity = $data['granularity'] ?? 'day';
        
        $cacheKey = "admin_analytics_timeseries_{$metric}_{$period}_{$granularity}";
        
        $series = Cache::remember($cacheKey, 300, function() use ($metric, $period, $granularity) {
            return $this->analyticsService->generateTimeseries($metric, $period, $granularity);
        });
        
        return response()->json([
            'data' => [
                'metric' => $metric,
                'period' => $period,
                'granularity' => $granularity,
                'series' => $series,
            ]
        ]);
    }

    public function topContent(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
        }
        
        $data = $request->validate([
            'type' => ['required', 'in:accommodations,guides,offerings,articles'],
            'metric' => ['nullable', 'in:bookings,favorites,views'],
            'limit' => ['nullable', 'integer', 'min:5', 'max:50'],
            'period' => ['nullable', 'in:7d,30d,90d,all'],
        ]);
        
        $type = $data['type'];
        $metric = $data['metric'] ?? 'bookings';
        $limit = $data['limit'] ?? 10;
        $period = $data['period'] ?? '30d';
        
        $cacheKey = "admin_analytics_top_{$type}_{$metric}_{$limit}_{$period}";
        
        $items = Cache::remember($cacheKey, 300, function() use ($type, $metric, $limit, $period) {
            return $this->analyticsService->getTopContent($type, $metric, $limit, $period);
        });
        
        return response()->json([
            'data' => [
                'type' => $type,
                'metric' => $metric,
                'period' => $period,
                'items' => $items,
            ]
        ]);
    }

    public function recentActivity(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
        }
        
        $data = $request->validate([
            'limit' => ['nullable', 'integer', 'min:5', 'max:50'],
        ]);
        
        $limit = $data['limit'] ?? 20;
        
        // Pour MVP, récupérer les derniers bookings avec event type
        $bookings = \DB::table('bookings')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('offerings', 'bookings.offering_id', '=', 'offerings.id')
            ->select([
                'bookings.id',
                'bookings.status',
                'bookings.created_at',
                'users.name as user_name',
                'offerings.title as offering_title'
            ])
            ->orderByDesc('bookings.created_at')
            ->limit($limit)
            ->get();
        
        $activities = $bookings->map(function($b) {
            $type = match($b->status) {
                'confirmed' => 'booking_confirmed',
                'cancelled' => 'booking_cancelled',
                default => 'booking_created',
            };
            
            $description = match($type) {
                'booking_confirmed' => "Booking #{$b->id} confirmed: {$b->offering_title}",
                'booking_cancelled' => "Booking #{$b->id} cancelled",
                default => "New booking #{$b->id} for {$b->offering_title}",
            };
            
            return [
                'id' => $b->id,
                'type' => $type,
                'description' => $description,
                'user' => ['name' => $b->user_name],
                'created_at' => $b->created_at,
            ];
        });
        
        return response()->json(['data' => $activities]);
    }

    public function export(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
        }
        
        $data = $request->validate([
            'type' => ['required', 'in:users,bookings,revenue,providers'],
            'format' => ['nullable', 'in:csv,json'],
            'period' => ['nullable', 'in:7d,30d,90d,1y,all'],
        ]);
        
        $type = $data['type'];
        $format = $data['format'] ?? 'csv';
        $period = $data['period'] ?? 'all';
        
        [$start, $end] = $this->parsePeriodForResponse($period);
        
        // Récupérer les données
        $query = null;
        $filename = "{$type}_export_" . now()->format('Ymd_His');
        
        switch ($type) {
            case 'users':
                $query = \DB::table('users')->select(['id', 'name', 'email', 'created_at', 'account_status']);
                if ($start && $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                }
                break;
                
            case 'bookings':
                $query = \DB::table('bookings')
                    ->join('users', 'bookings.user_id', '=', 'users.id')
                    ->join('offerings', 'bookings.offering_id', '=', 'offerings.id')
                    ->select([
                        'bookings.id',
                        'users.name as user_name',
                        'offerings.title as offering_title',
                        'bookings.start_date',
                        'bookings.amount',
                        'bookings.status',
                        'bookings.created_at'
                    ]);
                if ($start && $end) {
                    $query->whereBetween('bookings.created_at', [$start, $end]);
                }
                break;
                
            case 'revenue':
                $query = \DB::table('bookings')
                    ->where('status', 'confirmed')
                    ->select(['id', 'amount', 'currency', 'commission_amount', 'created_at']);
                if ($start && $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                }
                break;
                
            case 'providers':
                $query = \DB::table('users')
                    ->where('provider_status', 'approved')
                    ->select(['id', 'name', 'email', 'business_name', 'created_at']);
                break;
        }
        
        $results = $query->get()->toArray();
        
        if ($format === 'json') {
            return response()->json($results)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
        }
        
        // CSV format
        $csv = $this->generateCSV($results);
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }

    private function generateCSV(array $data): string
    {
        if (empty($data)) {
            return '';
        }
        
        $output = fopen('php://temp', 'r+');
        
        // Headers
        $firstRow = (array) $data[0];
        fputcsv($output, array_keys($firstRow));
        
        // Data
        foreach ($data as $row) {
            fputcsv($output, (array) $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    private function parsePeriodForResponse(string $period): array
    {
        $end = now()->toDateString();
        $start = match($period) {
            '7d' => now()->subDays(7)->toDateString(),
            '30d' => now()->subDays(30)->toDateString(),
            '90d' => now()->subDays(90)->toDateString(),
            '1y' => now()->subYear()->toDateString(),
            'all' => null,
            default => now()->subDays(30)->toDateString(),
        };
        
        return [$start, $end];
    }
}
