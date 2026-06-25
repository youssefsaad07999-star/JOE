<x-admin.layout>
    <x-slot:heading>Orders</x-slot:heading>
    <x-slot:subheading>{{ $orders->total() }} total orders</x-slot:subheading>

    <x-slot:actions>
        {{-- Filter Actions Form: Wraps gracefully into stacked block mechanics on mobile layouts --}}
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order # or customer..."
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-full sm:w-56
                       focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="status"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm flex-1 sm:flex-initial bg-white
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
                    class="bg-[#1C1C1C] text-white px-4 py-2 rounded-xl text-sm hover:bg-[#C85C6E] transition-colors flex-1 sm:flex-initial text-center font-medium">
                    Filter
                </button>

                @if (request('search') || request('status'))
                    <a href="{{ route('admin.orders.index') }}"
                        class="text-sm text-gray-500 hover:text-[#C85C6E] transition-colors pl-1 whitespace-nowrap">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </x-slot:actions>

    {{-- Safe Layout Table Card Container --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Scroll Wrapper: Isolates data structural density without throwing off layout width margins --}}
        <div class="overflow-x-auto w-full min-w-full align-middle">
            <table class="w-full min-w-[850px] table-auto">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/70">
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Order</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Customer</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Items</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Status</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Payment</th>
                        <th class="text-right px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Total</th>
                        <th class="text-right px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Date</th>
                        <th class="px-6 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">#{{ $order->id }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-800">{{ $order->user->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $order->user?->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $order->variants_count }} {{ Str::plural('item', $order->variants_count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-admin.badge :status="$order->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($order->payment)
                                    @if ($order->payment->method === 'card')
                                        <div class="flex flex-col items-start gap-1.5">
                                            <x-admin.badge :status="$order->payment->method" type="payment" />
                                            <x-admin.badge :status="$order->payment->status" type="payment" />
                                        </div>
                                    @else
                                        <div class="flex flex-col items-start gap-1.5">
                                            <x-admin.badge :status="$order->payment->method" type="payment" />
                                            <x-admin.badge :status="$order->payment->status" type="payment" />
                                        </div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-right text-gray-900 whitespace-nowrap">
                                ${{ number_format($order->total_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400 text-right whitespace-nowrap">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                    class="text-xs text-[#C85C6E] hover:underline font-medium px-2 py-1 bg-[#C85C6E]/5 hover:bg-[#C85C6E]/10 rounded-lg transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-gray-400 text-sm bg-gray-50/20">
                                No orders found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- @if ($orders->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                <div
                    class="w-full 
                    [&_nav]:flex [&_nav]:flex-col sm:[&_nav]:flex-row [&_nav]:items-center [&_nav]:justify-between [&_nav]:gap-4
                    [&_span]:rounded-xl [&_a]:rounded-xl 
                    [&_span[aria-current='page']_span]:bg-[#C85C6E] [&_span[aria-current='page']_span]:border-[#C85C6E] [&_span[aria-current='page']_span]:text-white
                    [&_a:hover]:text-[#C85C6E] [&_a:hover]:border-[#C85C6E]
                    [&_svg]:w-5 [&_svg]:h-5">
                    {{ $orders->withQueryString()->links() }}
                </div>
            </div>
        @endif --}}
        <x-paginator :paginator="$orders" />
    </div>
</x-admin.layout>
