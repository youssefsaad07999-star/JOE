<x-admin.layout>
    <x-slot:breadcrumb>
        [['label' => 'Orders', 'url' => route('admin.orders.index')],
        ['label' => '#' . $order->id, 'url' => null]]
    </x-slot:breadcrumb>

    <x-slot:heading>Order #{{ $order->id }}</x-slot:heading>
    <x-slot:subheading>Placed {{ $order->created_at->format('F d, Y · g:i A') }}</x-slot:subheading>

    <x-slot:actions>
        {{-- Status update form: Fluid structural stack on mobile, sleek horizontal inline row on desktop grids --}}
        <form action="{{ route('admin.orders.update', $order) }}" method="POST"
            class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">
            @csrf @method('PATCH')
            <select name="status"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm bg-white shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E] w-full sm:w-auto">
                @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                class="bg-[#1C1C1C] text-white px-4 py-2 rounded-xl text-sm text-center
                       hover:bg-[#C85C6E] transition-colors font-medium whitespace-nowrap shadow-sm">
                Update Status
            </button>
        </form>
    </x-slot:actions>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Items Left Column Area --}}
        <div class="lg:col-span-2 space-y-4">

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold text-gray-900">Items</h2>
                    <span class="text-xs bg-gray-50 text-gray-500 px-2 py-0.5 rounded-md border border-gray-100">
                        {{ $order->variants->count() }} {{ Str::plural('item', $order->variants->count()) }}
                    </span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach ($order->variants as $variant)
                        @php
                            $img = $variant->product->images->where('color_id', $variant->color_id)->first();
                        @endphp
                        <div class="flex gap-4 p-4 sm:p-5 items-start">
                            <div
                                class="w-16 h-20 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0 shadow-sm">
                                @if ($img)
                                    <img src="{{ asset('storage/' . $img->image_path) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                                        <span class="text-[10px] text-gray-300 uppercase tracking-wider font-medium">No
                                            Img</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm text-gray-900 line-clamp-2 sm:line-clamp-none">
                                    {{ $variant->product->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $variant->size->name }} · {{ ucfirst($variant->color->name) }}
                                </p>
                                <p class="text-xs text-gray-300 font-mono mt-0.5 tracking-wide">{{ $variant->sku }}</p>

                                {{-- Item Details Meta: Switches from an easy-to-tap block structure to clean side-by-side spacing --}}
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-3 pt-2 border-t border-gray-50 sm:border-none">
                                    <span
                                        class="text-xs bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full w-fit font-medium">
                                        Qty: {{ $variant->pivot->quantity }}
                                    </span>
                                    <div class="text-left sm:text-right">
                                        <p class="text-sm font-semibold text-gray-900">
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

            {{-- Timeline Card Component --}}
            <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6 border border-gray-100">
                <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold mb-5 text-gray-900">Order Timeline</h2>
                @php
                    $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                    $labels = ['Order Placed', 'Processing', 'Shipped', 'Delivered'];
                    $currentIdx = array_search($order->status, $statuses) ?? 0;
                @endphp

                {{-- Responsive Axis Control: Renders vertically on mobile viewports and horizontally on desktop dashboards --}}
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1.5 sm:gap-0">
                    @foreach ($statuses as $idx => $s)
                        <div
                            class="flex flex-row sm:flex-col items-center sm:text-center gap-4 sm:gap-0 flex-1 min-w-0 w-full sm:w-auto">
                            <div
                                class="w-8 h-8 rounded-full border-2 flex items-center justify-center flex-shrink-0 z-10 shadow-sm
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
                                class="sm:mt-2 text-xs truncate max-w-full font-medium
                                         {{ $idx <= $currentIdx ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $labels[$idx] }}
                            </span>
                        </div>
                        @if (!$loop->last)
                            {{-- Connector line logic adapts smoothly: vertical line segment for phones, flat ribbon bridge for laptops --}}
                            <div
                                class="w-0.5 h-6 ml-4 my-0.5 -mt-1 sm:mt-3.5 sm:my-0 sm:h-0.5 sm:w-full sm:-mx-4 transition-colors duration-300
                                        {{ $idx < $currentIdx ? 'bg-[#C85C6E]' : 'bg-gray-100' }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar Right Column Area --}}
        <div class="space-y-4">

            {{-- Summary Card --}}
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100">
                <h3 class="font-semibold text-sm mb-4 text-gray-900">Order Summary</h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span
                            class="text-gray-800 font-medium">${{ number_format($order->total_price - $order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Shipping</span>
                        <span
                            class="text-gray-800 font-medium">{{ $order->shipping_cost > 0 ? '$' . number_format($order->shipping_cost, 2) : 'Free' }}</span>
                    </div>
                    <div
                        class="flex justify-between font-semibold border-t border-gray-100 pt-3 mt-3 text-gray-900 text-base">
                        <span>Total</span>
                        <span>${{ number_format($order->total_price, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Customer Card --}}
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100">
                <h3 class="font-semibold text-sm mb-3 text-gray-900">Customer</h3>
                <p class="text-sm font-medium text-gray-800">
                    {{ ucfirst($order->user?->name) }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $order->user?->email }}</p>
                <a href="{{ route('admin.users.show', $order->user) }}"
                    class="text-xs text-[#C85C6E] hover:underline mt-3 inline-block font-medium">
                    View profile →
                </a>
            </div>

            {{-- Shipping Details Card --}}
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100">
                <h3 class="font-semibold text-sm mb-3 text-gray-900">Shipping Address</h3>
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
                        <p class="text-sm mt-0.5 font-medium text-gray-800">{{ $order->shipping_method_name }}</p>
                    </div>
                @endif
            </div>

            {{-- Payment Information Card --}}
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100">
                <h3 class="font-semibold text-sm mb-3 text-gray-900">Payment</h3>
                @if ($order->payment)
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Method</span>
                            <span
                                class="capitalize text-gray-800 font-medium">{{ str_replace('_', ' ', $order->payment->method) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Status</span>
                            <x-admin.badge :status="$order->payment->status" type="payment" />
                        </div>
                        @if ($order->payment->transaction_id)
                            <div class="pt-2.5 border-t border-gray-100">
                                <p class="text-xs text-gray-400">Transaction ID</p>
                                <p
                                    class="text-xs font-mono mt-1 bg-gray-50 p-2 rounded-lg text-gray-600 break-all select-all border border-gray-100/70">
                                    {{ $order->payment->transaction_id }}
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">No payment record found</p>
                @endif
            </div>
        </div>
    </div>
</x-admin.layout>
