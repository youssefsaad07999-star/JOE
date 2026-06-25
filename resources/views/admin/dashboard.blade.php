<x-admin.layout>
    <x-slot:heading>Dashboard</x-slot:heading>
    <x-slot:subheading>Welcome back, {{ auth()->user()->name }}</x-slot:subheading>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-admin.stat-card label="Revenue This Month" value="${{ number_format($revenueThisMonth, 2) }}"
            change="{{ $revenueChange >= 0 ? '+' : '' }}{{ $revenueChange }}%"
            trend="{{ $revenueChange >= 0 ? 'up' : 'down' }}"
            icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 6v1m0 4V12"
            color="rose" />

        <x-admin.stat-card label="Orders This Month" value="{{ $ordersThisMonth }}"
            change="{{ $ordersChange >= 0 ? '+' : '' }}{{ $ordersChange }}%"
            trend="{{ $ordersChange >= 0 ? 'up' : 'down' }}" icon="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
            color="blue" />

        <x-admin.stat-card label="New Customers" value="{{ $usersThisMonth }}"
            change="{{ $usersChange >= 0 ? '+' : '' }}{{ $usersChange }}%"
            trend="{{ $usersChange >= 0 ? 'up' : 'down' }}"
            icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
            color="emerald" />

        <x-admin.stat-card label="Out of Stock" value="{{ $outOfStock }}"
            icon="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
            color="amber" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-4 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-6">
                <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold text-gray-900">Revenue — Last 7 Days</h2>
                <span class="text-sm text-gray-400 font-light">
                    Total: ${{ number_format($chartData->sum('revenue'), 2) }}
                </span>
            </div>

            <div class="flex items-end gap-1 sm:gap-2 h-40 pt-4">
                @foreach ($chartData as $day)
                    @php
                        $height = $chartMax > 0 ? round(($day['revenue'] / $chartMax) * 100) : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1.5 min-w-0">
                        <span class="text-[10px] sm:text-xs text-gray-400 font-light truncate max-w-full block h-4">
                            @if ($day['revenue'] > 0)
                                ${{ number_format($day['revenue'], 0) }}
                            @endif
                        </span>
                        <div class="w-full rounded-t-lg transition-all duration-500 relative group"
                            style="height: {{ max($height, 4) }}%; background: {{ $height > 0 ? '#C85C6E' : '#F3F4F6' }}">
                            {{-- Tooltip --}}
                            <div
                                class="absolute -top-8 left-1/2 -translate-x-1/2 bg-[#1C1C1C] text-white
                                        text-[10px] px-2 py-1 rounded whitespace-nowrap opacity-0
                                        group-hover:opacity-100 transition-opacity pointer-events-none z-10 shadow-md">
                                ${{ number_format($day['revenue'], 2) }}
                            </div>
                        </div>
                        <span
                            class="text-[10px] sm:text-xs text-gray-400 font-medium truncate max-w-full block">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Order Status Breakdown --}}
        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm">
            <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold mb-5 text-gray-900">Orders by Status</h2>
            @php
                $total = $statusBreakdown->sum();
                $statusColors = [
                    'pending' => 'bg-amber-400',
                    'processing' => 'bg-blue-400',
                    'shipped' => 'bg-indigo-400',
                    'delivered' => 'bg-emerald-400',
                    'cancelled' => 'bg-red-400',
                ];
            @endphp
            <div class="space-y-4">
                @foreach ($statusBreakdown as $status => $count)
                    @php $pct = $total > 0 ? round(($count / $total) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="capitalize font-light text-gray-600">{{ $status }}</span>
                            <span class="font-medium text-gray-900">{{ $count }}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $statusColors[$status] ?? 'bg-gray-400' }}"
                                style="width: {{ $pct }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Recent Orders --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold text-gray-900">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}"
                    class="text-xs text-[#C85C6E] hover:underline font-medium">View all &rarr;</a>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full min-w-[500px] table-auto">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/75">
                            <th
                                class="text-left px-4 sm:px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Order
                            </th>
                            <th
                                class="text-left px-4 sm:px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Customer
                            </th>
                            <th
                                class="text-left px-4 sm:px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Status
                            </th>
                            <th
                                class="text-right px-4 sm:px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 sm:px-6 py-3.5 vertical-align-middle">
                                    <a href=""
                                        class="text-sm font-medium text-gray-900 hover:text-[#C85C6E] transition-colors">
                                        #{{ $order->id }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->diffForHumans() }}
                                    </p>
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-sm text-gray-600 vertical-align-middle">
                                    {{ $order->user?->name ?? '—' }}
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 vertical-align-middle">
                                    <x-admin.badge :status="$order->status" />
                                </td>
                                <td
                                    class="px-4 sm:px-6 py-3.5 text-sm font-semibold text-gray-900 text-right vertical-align-middle">
                                    ${{ number_format($order->total_price, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-4 sm:px-6 py-12 text-center text-gray-400 text-sm bg-white">
                                    No orders yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Low Stock --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold text-gray-900">Low Stock</h2>
                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">
                    Alert
                </span>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($lowStock as $variant)
                    <div class="px-4 sm:px-6 py-3.5 flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $variant->product->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5 truncate">
                                {{ $variant->size->name }} · {{ ucfirst($variant->color->name) }}
                            </p>
                        </div>
                        <span
                            class="text-sm font-bold {{ $variant->stock_quantity <= 2 ? 'text-red-600' : 'text-amber-600' }} shrink-0">
                            {{ $variant->stock_quantity }} left
                        </span>
                    </div>
                @empty
                    <div class="px-4 sm:px-6 py-12 text-center text-gray-400 text-sm bg-white">
                        All variants well stocked
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-admin.layout>
