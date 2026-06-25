<x-layout>
    {{-- Fluid outer paddings adjust seamlessly between phone and desktop scales --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

        <div class="mb-8 sm:mb-10">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">Account</p>
            <h1 class="font-['Cormorant_Garamond'] text-4xl sm:text-5xl font-light">My Orders</h1>
        </div>

        @forelse($orders as $order)
            <div class="bg-white rounded-2xl shadow-sm mb-4 overflow-hidden border border-gray-100 sm:border-none">

                {{-- Order Header --}}
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between px-4 sm:px-6 py-4 bg-gray-50 border-b border-gray-100 gap-4">

                    {{-- Grid on mobile (2-columns) turns into smooth flex alignment on larger screens --}}
                    <div
                        class="grid grid-cols-2 gap-x-4 gap-y-3 sm:flex sm:flex-wrap sm:gap-6 items-center w-full sm:w-auto">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Order</p>
                            <p class="text-sm font-semibold">#{{ $order->id }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Date</p>
                            <p class="text-sm text-gray-700">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Total</p>
                            <p class="text-sm font-semibold text-gray-900">${{ number_format($order->total_price, 2) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Status</p>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
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

                        @if ($order->payment ?? false)
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Payment Method</p>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                                    {{ match ($order->payment->method) {
                                        'card' => 'bg-emerald-100 text-emerald-700',
                                        'cash on delivery' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ ucfirst($order->payment->method ?? 'Error') }}
                                </span>
                            </div>
                        @endif

                        @if ($order->payment ?? false)
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Payment Status</p>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                                    {{ match ($order->payment->status) {
                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'refunded' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ ucfirst($order->payment->status ?? 'Error') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- View Details CTA: Full-width border element on mobile, clean row text on desktop --}}
                    <div class="pt-3 sm:pt-0 border-t border-gray-200/60 sm:border-none flex justify-end">
                        <a href="{{ route('order.show', $order) }}"
                            class="text-sm text-[#C85C6E] hover:underline font-medium whitespace-nowrap inline-flex items-center gap-1">
                            View Details →
                        </a>
                    </div>
                </div>

                {{-- Order Items Preview --}}
                {{-- Dynamic thumbnails adapt size (`w-12 h-14` scaling up to `sm:w-14 sm:h-16`) to guarantee fitment on small viewports --}}
                <div class="px-4 sm:px-6 py-4 flex flex-wrap gap-2.5 sm:gap-3 items-center">
                    @foreach ($order->variants as $variant)
                        <div
                            class="w-12 h-14 sm:w-14 sm:h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100">
                            @php
                                $img = $variant->product->images->where('color_id', $variant->color_id)->first();
                            @endphp
                            @if ($img)
                                <img src="{{ asset('storage/' . $img->image_path) }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if ($order->variants->count() > 4)
                        <div
                            class="w-12 h-14 sm:w-14 sm:h-16 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                            <span
                                class="text-xs text-gray-500 font-semibold">+{{ $order->variants->count() - 4 }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-16 sm:py-24 px-4">
                <div
                    class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h2 class="font-['Cormorant_Garamond'] text-2xl sm:text-3xl font-light text-gray-500 mb-2">No orders yet
                </h2>
                <p class="text-gray-400 text-sm font-light mb-6 max-w-xs mx-auto">Your orders will appear here once you
                    make a purchase.</p>
                <a href="/"
                    class="inline-block bg-[#1C1C1C] text-white px-8 py-3.5 rounded-full text-sm hover:bg-[#C85C6E] transition-colors w-full sm:w-auto">
                    Start Shopping
                </a>
            </div>
        @endforelse

        <x-paginator :paginator="$orders" />
    </div>
</x-layout>
