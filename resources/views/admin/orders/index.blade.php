<x-admin.layout>
    <x-slot:heading>Orders</x-slot:heading>
    <x-slot:subheading>{{ $orders->total() }} total orders</x-slot:subheading>

    <x-slot:actions>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order # or customer..."
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-56
                          focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">

            <select name="status"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]"
                onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="bg-[#1C1C1C] text-white px-4 py-2 rounded-xl text-sm hover:bg-[#C85C6E] transition-colors">
                Filter
            </button>
            @if (request('search') || request('status'))
                <a href="{{ route('admin.orders.index') }}"
                    class="text-sm text-gray-500 hover:text-[#C85C6E] transition-colors">Clear</a>
            @endif
        </form>
    </x-slot:actions>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Order
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Customer
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Items
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Payment
                    </th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Total
                    </th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Date
                    </th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold">#{{ $order->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium">
                                {{ $order->user->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $order->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $order->variants_count }} {{ Str::plural('item', $order->variants_count) }}
                        </td>
                        <td class="px-6 py-4">
                            <x-admin.badge :status="$order->status" />
                        </td>
                        <td class="px-6 py-4">
                            @if ($order->payment)
                                <x-admin.badge :status="$order->payment->status" type="payment" />
                            @else
                                <x-admin.badge status="cash on delivery" type="payment" />
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-right">
                            ${{ number_format($order->total_price, 2) }}
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400 text-right whitespace-nowrap">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}"
                                class="text-xs text-[#C85C6E] hover:underline font-medium">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-400 text-sm">
                            No orders found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>
