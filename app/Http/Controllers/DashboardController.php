<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // If user has no active tenant, return empty dashboard
        if (!auth()->user()->hasActiveTenant()) {
            return Inertia::render('Dashboard', [
                'recentBookings' => [],
                'propertyStatistics' => [],
                'upcomingCheckouts' => [],
                'occupancyRate' => 0,
                'totalRevenue' => 0,
                'totalProperties' => 0,
                'totalRooms' => 0,
                'totalCustomers' => 0,
                'bookingsChart' => [],
                'hasActiveTenant' => false
            ]);
        }

        $tenant = auth()->user()->getActiveTenant();

        // Recent bookings (last 5)
        $recentBookings = Booking::where('tenant_id', $tenant->id)
            ->with(['room.property', 'bookingStatus', 'bookingSource'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Properties with occupancy statistics
        $properties = Property::where('tenant_id', $tenant->id)->get();
        $propertyStatistics = [];

        foreach ($properties as $property) {
            $totalRooms = $property->rooms()->count();
            $occupiedRooms = $property->rooms()
                ->whereHas('bookings', function ($query) {
                    $today = Carbon::today()->format('Y-m-d');
                    $query->where('check_in', '<=', $today)
                        ->where('check_out', '>', $today);
                })
                ->count();

            $propertyStatistics[] = [
                'id' => $property->id,
                'name' => $property->name,
                'totalRooms' => $totalRooms,
                'occupiedRooms' => $occupiedRooms,
                'availableRooms' => $totalRooms - $occupiedRooms,
                'occupancyRate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0
            ];
        }

        // Upcoming checkouts (next 7 days)
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        $upcomingCheckouts = Booking::where('tenant_id', $tenant->id)
            ->whereBetween('check_out', [$today, $nextWeek])
            ->with(['room.property'])
            ->orderBy('check_out')
            ->get();

        // Overall statistics
        $totalProperties = $properties->count();
        $totalRooms = Room::where('tenant_id', $tenant->id)->count();
        $totalCustomers = Customer::where('tenant_id', $tenant->id)->count();

        // Calculate total occupancy rate
        $allRooms = Room::where('tenant_id', $tenant->id)->count();
        $occupiedRooms = Room::where('tenant_id', $tenant->id)
            ->whereHas('bookings', function ($query) {
                $today = Carbon::today()->format('Y-m-d');
                $query->where('check_in', '<=', $today)
                    ->where('check_out', '>', $today);
            })
            ->count();

        $occupancyRate = $allRooms > 0 ? round(($occupiedRooms / $allRooms) * 100) : 0;

        // Calculate total revenue (current month)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalRevenue = Booking::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('price');

        // Bookings chart data (last 6 months)
        $bookingsChart = $this->getBookingsChartData($tenant->id);

        return Inertia::render('Dashboard', [
            'recentBookings' => $recentBookings,
            'propertyStatistics' => $propertyStatistics,
            'upcomingCheckouts' => $upcomingCheckouts,
            'occupancyRate' => $occupancyRate,
            'totalRevenue' => $totalRevenue,
            'totalProperties' => $totalProperties,
            'totalRooms' => $totalRooms,
            'totalCustomers' => $totalCustomers,
            'bookingsChart' => $bookingsChart,
            'hasActiveTenant' => true
        ]);
    }

    private function getBookingsChartData($tenantId)
    {
        $data = [];
        $today = Carbon::today();

        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $bookingsCount = Booking::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $data[] = [
                'month' => $month->format('M'),
                'count' => $bookingsCount
            ];
        }

        return $data;
    }
}
