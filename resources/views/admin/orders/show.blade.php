<x-admin.layout>
    <x-slot:breadcrumb>
        [['label' => 'Orders', 'url' => route('admin.orders.index')],
        ['label' => '#' . $order->id, 'url' => null]]
    </x-slot:breadcrumb>

    <x-slot:heading>Order #{{ $order->id }}</x-slot:heading>
    <x-slot:subheading>Placed {{ $order->created_at->format('F d, Y · g:i A') }}</x-slot:subheading>

    <x-slot:actions>
        {{-- Status update --}}
        <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="flex items-center gap-2">
            @csrf @method('PATCH')
            <select name="status"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                class="bg-[#1C1C1C] text-white px-4 py-2 rounded-xl text-sm
                           hover:bg-[#C85C6E] transition-colors font-medium">
                Update Status
            </button>
        </form>
    </x-slot:actions>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Items --}}
        <div class="lg:col-span-2 space-y-4">

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold">Items</h2>
                    <span class="text-xs text-gray-400">{{ $order->variants->count() }} items</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach ($order->variants as $variant)
                        @php
                            $img = $variant->product->images->where('color_id', $variant->color_id)->first();
                        @endphp
                        <div class="flex gap-4 p-5">
                            <div class="w-16 h-20 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                @if ($img)
                                    <img src="{{ asset('storage/' . $img->image_path) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm">{{ $variant->product->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $variant->size->name }} · {{ ucfirst($variant->color->name) }}
                                </p>
                                <p class="text-xs text-gray-300 font-mono mt-0.5">{{ $variant->sku }}</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                        Qty: {{ $variant->pivot->quantity }}
                                    </span>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold">
                                            ${{ number_format($variant->pivot->subtotal, 2) }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            ${{ number_format($variant->pivot->unit_price, 2) }} each
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold mb-5">Order Timeline</h2>
                @php
                    $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                    $labels = ['Order Placed', 'Processing', 'Shipped', 'Delivered'];
                    $currentIdx = array_search($order->status, $statuses) ?? 0;
                @endphp
                <div class="flex items-center">
                    @foreach ($statuses as $idx => $s)
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div
                                class="w-8 h-8 rounded-full border-2 flex items-center justify-center
                                        {{ $idx <= $currentIdx ? 'bg-[#C85C6E] border-[#C85C6E]' : 'bg-white border-gray-200' }}">
                                @if ($idx <= $currentIdx)
                                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                                @endif
                            </div>
                            <span
                                class="mt-2 text-xs whitespace-nowrap
                                         {{ $idx <= $currentIdx ? 'font-medium' : 'text-gray-400' }}">
                                {{ $labels[$idx] }}
                            </span>
                        </div>
                        @if (!$loop->last)
                            <div
                                class="flex-1 h-0.5 mx-2 mb-5
                                        {{ $idx < $currentIdx ? 'bg-[#C85C6E]' : 'bg-gray-100' }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Summary --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-sm mb-4">Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span>${{ number_format($order->total_price - $order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Shipping</span>
                        <span>{{ $order->shipping_cost > 0 ? '$' . number_format($order->shipping_cost, 2) : 'Free' }}</span>
                    </div>
                    <div class="flex justify-between font-semibold border-t border-gray-100 pt-2 mt-2">
                        <span>Total</span>
                        <span>${{ number_format($order->total_price, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Customer --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-sm mb-3">Customer</h3>
                <p class="text-sm font-medium">
                    {{ ucfirst($order->user?->name) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $order->user?->email }}</p>
                <a href="{{ route('admin.users.show', $order->user) }}"
                    class="text-xs text-[#C85C6E] hover:underline mt-2 inline-block">
                    View profile →
                </a>
            </div>

            {{-- Shipping --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-sm mb-3">Shipping Address</h3>
                <p class="text-sm text-gray-600 font-light leading-relaxed">
                    {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                    {{ $order->shipping_address }}
                    @if ($order->shipping_address2)
                        , {{ $order->shipping_address2 }}
                    @endif
                    <br>
                    {{ $order->shipping_city }}, {{ $order->shipping_postal_code }}<br>
                    {{ $order->shipping_country }}
                </p>
                @if ($order->shipping_method_name)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Method</p>
                        <p class="text-sm mt-0.5">{{ $order->shipping_method_name }}</p>
                    </div>
                @endif
            </div>

            {{-- Payment --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-sm mb-3">Payment</h3>
                @if ($order->payment)
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Method</span>
                            <span class="capitalize">{{ str_replace('_', ' ', $order->payment->method) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            <x-admin.badge :status="$order->payment->status" type="payment" />
                        </div>
                        @if ($order->payment->transaction_id)
                            <div class="pt-2 border-t border-gray-100">
                                <p class="text-xs text-gray-400">Transaction ID</p>
                                <p class="text-xs font-mono mt-0.5 break-all">{{ $order->payment->transaction_id }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-400">No payment record</p>
                @endif
            </div>
        </div>
    </div>
</x-admin.layout>
