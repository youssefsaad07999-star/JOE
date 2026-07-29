<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\OrderStatus;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsOverview extends StatsOverviewWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        // 2. Calculate Gross Revenue metrics
        $totalRevenue = Order::where('status', OrderStatus::Delivered)->sum('total_price');
        $thisMonthRevenue = Order::where('status', OrderStatus::Delivered)
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total_price');

        // 3. Calculate Average Order Value (AOV)
        $completedOrdersCount = Order::where('status', OrderStatus::Delivered)->count();
        $averageOrderValue = $completedOrdersCount > 0 ? ($totalRevenue / $completedOrdersCount) : 0;

        // 4. Track Operational Backlog
        $pendingOrders = Order::whereIn('status', [OrderStatus::Pending, OrderStatus::Processing])->count();

        return [
            Stat::make('Total Revenue', '$'.number_format($totalRevenue, 2)) // Assuming cents storage
                ->description('$'.number_format($thisMonthRevenue, 2).' grossed this month')
                ->descriptionIcon($thisMonthRevenue > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($thisMonthRevenue > 0 ? 'success' : 'gray'),

            Stat::make('Average Order Value', '$'.number_format($averageOrderValue, 2))
                ->description('Lifetime cross-basket average')
                ->chart([15, 22, 21, 28, 25, 32, 30]) // Renders a sleek subtle mini-sparkline
                ->color('primary'),

            Stat::make('Fulfillment Queue', $pendingOrders)
                ->description($pendingOrders === 0 ? 'All orders dispatched' : 'Requires immediate processing')
                ->icon('heroicon-m-shopping-bag')
                ->color(match (true) {
                    $pendingOrders > 20 => 'danger',
                    $pendingOrders > 0 => 'warning',
                    default => 'success',
                }),
        ];
    }
}
