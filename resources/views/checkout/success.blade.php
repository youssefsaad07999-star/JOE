<x-layout>
    <div class="max-w-2xl mx-auto px-6 py-20 text-center">

        {{-- Success Icon --}}
        <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-8">
            <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-3">Order Confirmed</p>
        <h1 class="font-['Cormorant_Garamond'] text-5xl font-light mb-4">Thank You!</h1>
        <p class="text-gray-500 font-light leading-relaxed">
            Your order has been placed
            successfully.
            We've sent a confirmation to <span class="font-medium">{{ $order->user->email }}</span>.
        </p>

        <div class="bg-white rounded-2xl p-6 mt-8 text-left shadow-sm">
            <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold mb-4">Order Details</h2>
            <div class="space-y-1 text-sm">
                @foreach ($order->variants as $variant)
                    <div class="flex justify-between items-center ">
                        <div>
                            <p class="font-medium">{{ $variant->product->name }}</p>
                            <p class="text-gray-400 text-xs">Qty: {{ $variant->pivot->quantity }}</p>
                        </div>
                        <span class="font-semibold">${{ number_format($variant->pivot->subtotal, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="font-medium">Shipping Cost</p>
                    </div>
                    <span class="font-semibold">${{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                <div class="flex justify-between font-semibold text-base pt-2">
                    <span>Total</span>
                    <span>${{ number_format($order->total_price, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">

            <a href="{{ route('order.show', $order) }}"
                class="px-8 py-3.5 bg-[#1C1C1C] text-white rounded-full text-sm font-medium hover:bg-[#C85C6E] transition-colors">
                Track Order
            </a>
            <a href="/"
                class="px-8 py-3.5 border border-gray-200 text-gray-600 rounded-full text-sm font-medium hover:border-gray-400 transition-colors">
                Continue Shopping
            </a>
        </div>

    </div>
</x-layout>
