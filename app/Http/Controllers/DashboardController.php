<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $now = now();
        $thisMonth = now()->copy()->startOfMonth();
        $lastMonth = now()->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->copy()->subMonth()->endOfMonth();

        // Revenue
        $revenueThisMonth = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total_price');

        $revenueLastMonth = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->sum('total_price');

        $revenueChange = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100)
            : 0;

        // Orders
        $ordersThisMonth = Order::where('created_at', '>=', $thisMonth)->count();
        $ordersLastMonth = Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        $ordersChange = $ordersLastMonth > 0
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100)
            : 0;

        // Users
        $usersThisMonth = User::where('created_at', '>=', $thisMonth)->count();
        $usersLastMonth = User::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        $usersChange = $usersLastMonth > 0
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100)
            : 0;

        // Low Stock - Varants with 5 or less stock quantity
        $lowStock = ProductVariant::with(['product', 'size', 'color'])
            ->where('stock_quantity', '<=', 5)
            ->where('stock_quantity', '>=', 0)
            ->orderBy('stock_quantity')
            // ->take(5)
            ->get();

        $outOfStock = ProductVariant::where('stock_quantity', 0)->count();

        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        $chartData = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'revenue' => Order::where('status', '!=', 'cancelled')
                    ->whereDate('created_at', $date)
                    ->sum('total_price'),
            ];
        });

        $chartMax = $chartData->max('revenue') ?: 1;

        $statusBreakdown = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.dashboard', compact(
            'revenueThisMonth', 'revenueChange',
            'ordersThisMonth', 'ordersChange',
            'usersThisMonth', 'usersChange',
            'lowStock', 'outOfStock',
            'recentOrders',
            'chartData', 'chartMax',
            'statusBreakdown'
        ));

    }
}
