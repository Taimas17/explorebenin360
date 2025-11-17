<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Offering;
use App\Models\Place;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * KPIs globaux de la plateforme
     * GET /api/v1/admin/dashboard/kpis
     */
    public function kpis(Request $request)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $totalProviders = User::where('provider_status', 'approved')->count();
        $pendingProviders = User::where('provider_status', 'pending')->count();

        $totalPlaces = Place::where('status', 'published')->count();
        $totalOfferings = Offering::where('status', 'published')->count();
        $totalArticles = Article::where('status', 'published')->count();

        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        $totalRevenue = Booking::where('status', 'confirmed')->sum('amount');
        $totalCommission = Booking::where('status', 'confirmed')->sum('commission_amount');
        $revenueThisMonth = Booking::where('status', 'confirmed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        return response()->json([
            'data' => [
                'users' => [
                    'total' => $totalUsers,
                    'new_this_month' => $newUsersThisMonth,
                ],
                'providers' => [
                    'total' => $totalProviders,
                    'pending' => $pendingProviders,
                ],
                'content' => [
                    'places' => $totalPlaces,
                    'offerings' => $totalOfferings,
                    'articles' => $totalArticles,
                ],
                'bookings' => [
                    'total' => $totalBookings,
                    'confirmed' => $confirmedBookings,
                    'pending' => $pendingBookings,
                ],
                'revenue' => [
                    'total' => (float) $totalRevenue,
                    'commission' => (float) $totalCommission,
                    'this_month' => (float) $revenueThisMonth,
                    'currency' => 'XOF',
                ],
            ]
        ]);
    }

    /**
     * Série temporelle des réservations
     * GET /api/v1/admin/dashboard/bookings-timeseries
     */
    public function bookingsTimeseries(Request $request)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $days = min((int) $request->get('days', 30), 365);

        $startDate = Carbon::now()->subDays($days)->startOfDay();

        $timeseries = Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'value' => (int) $item->count,
                ];
            });

        return response()->json(['data' => $timeseries]);
    }

    /**
     * Activités récentes de la plateforme
     * GET /api/v1/admin/dashboard/recent-activity
     */
    public function recentActivity(Request $request)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $limit = min((int) $request->get('limit', 20), 100);

        $activities = [];

        $recentBookings = Booking::with(['user', 'offering'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentBookings as $booking) {
            $activities[] = [
                'id' => 'booking-' . $booking->id,
                'type' => 'booking',
                'text' => sprintf(
                    'Réservation #%d - %s a réservé "%s"',
                    $booking->id,
                    optional($booking->user)->name,
                    optional($booking->offering)->title
                ),
                'status' => $booking->status,
                'created_at' => $booking->created_at,
            ];
        }

        $recentProviders = User::where('provider_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentProviders as $provider) {
            $activities[] = [
                'id' => 'provider-' . $provider->id,
                'type' => 'provider_application',
                'text' => sprintf(
                    'Nouvelle demande provider : %s (%s)',
                    $provider->business_name ?? $provider->name,
                    $provider->email
                ),
                'status' => 'pending',
                'created_at' => $provider->created_at,
            ];
        }

        usort($activities, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return response()->json([
            'data' => array_slice($activities, 0, $limit)
        ]);
    }

    /**
     * Statistiques de conversion
     * GET /api/v1/admin/dashboard/conversion-stats
     */
    public function conversionStats(Request $request)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $totalOfferings = Offering::where('status', 'published')->count();
        $offeringsWithBookings = Offering::where('status', 'published')
            ->has('bookings')
            ->count();

        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        $conversionRate = $totalBookings > 0
            ? round(($confirmedBookings / $totalBookings) * 100, 2)
            : 0;

        $offeringUtilizationRate = $totalOfferings > 0
            ? round(($offeringsWithBookings / $totalOfferings) * 100, 2)
            : 0;

        $cancellationRate = $totalBookings > 0
            ? round(($cancelledBookings / $totalBookings) * 100, 2)
            : 0;

        $averageBookingValue = Booking::where('status', 'confirmed')->avg('amount') ?? 0;

        return response()->json([
            'data' => [
                'booking_conversion_rate' => $conversionRate,
                'offering_utilization_rate' => $offeringUtilizationRate,
                'average_booking_value' => (float) $averageBookingValue,
                'cancellation_rate' => $cancellationRate,
            ]
        ]);
    }
}
