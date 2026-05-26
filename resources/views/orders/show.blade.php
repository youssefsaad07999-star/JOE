<x-layout>
    <div class="max-w-4xl mx-auto px-6 py-10">

        <div class="mb-10">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">Account</p>
            <h1 class="font-['Cormorant_Garamond'] text-5xl font-light">My Orders</h1>
        </div>

        @forelse($orders as $order)
                <div class="bg-white rounded-2xl shadow-sm mb-4 overflow-hidden">

                    {{-- Order Header --}}
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-100 gap-3">
                        <div class="flex flex-wrap gap-6">
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Order</p>
                                <p class="text-sm font-semibold">#{{ $order->id }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Date</p>
                                <p class="text-sm">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Total</p>
                                <p class="text-sm font-semibold">${{ number_format($order->total, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Status</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ match ($order->status) {
                'delivered' => 'bg-emerald-100 text-emerald-700',
                'shipped' => 'bg-blue-100 text-blue-700',
                'processing' => 'bg-amber-100 text-amber-700',
                'cancelled' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-600',
            } }}">
                                    {{ ucfirst($order->status ?? 'pending') }}
                                </span>
                            </div>
                        </div>
                        {{-- {{ route('orders.show', $order) }} --}}
                        <a href="" class="text-sm text-[#C85C6E] hover:underline font-medium whitespace-nowrap">
                            View Details →
                        </a>
                    </div>

                    {{-- Order Items Preview --}}
                    <div class="px-6 py-4 flex gap-3">
                        @foreach($order->items->take(4) as $item)
                            <div class="w-14 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @if($order->items->count() > 4)
                            <div class="w-14 h-16 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs text-gray-500 font-medium">+{{ $order->items->count() - 4 }}</span>
                            </div>
                        @endif
                    </div>
                </div>
        @empty
            <div class="text-center py-24">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h2 class="font-['Cormorant_Garamond'] text-3xl font-light text-gray-500 mb-3">No orders yet</h2>
                <p class="text-gray-400 text-sm font-light mb-8">Your orders will appear here once you make a purchase.</p>
                <a href="/"
                    class="inline-block bg-[#1C1C1C] text-white px-8 py-3.5 rounded-full text-sm hover:bg-[#C85C6E] transition-colors">
                    Start Shopping
                </a>
            </div>
        @endforelse

        @if(method_exists($orders, 'links'))
            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </div>
</x-layout>