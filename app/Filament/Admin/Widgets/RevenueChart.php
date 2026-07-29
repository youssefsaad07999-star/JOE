<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\OrderStatus;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Revenue Chart';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $orders = Order::query()
            ->where('status', OrderStatus::Delivered)
            ->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])
            ->get()
            ->groupBy(fn ($order) => $order->created_at->format('n')); // Groups by month numbers (1 - 12)

        // 2. Generate data points for all 12 months sequentially
        $chartData = collect(range(1, 12))->map(function ($monthNum) use ($orders) {
            $monthOrders = $orders->get($monthNum);

            if (! $monthOrders) {
                return 0;
            }

            return $monthOrders->sum('total_price') / 100;
        })->toArray();

        // 3. Define the static calendar labels
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        return [
            'datasets' => [
                [
                    'label' => 'Gross Earnings ($)',
                    'data' => $chartData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'fill' => 'start',
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
