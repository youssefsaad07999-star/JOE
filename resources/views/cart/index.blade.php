<x-layout>
    <div class="max-w-6xl mx-auto px-6 py-10">

        <h1 class="font-['Cormorant_Garamond'] text-5xl font-light mb-2">Shopping Bag</h1>
        <p class="text-gray-500 font-light text-sm mb-10">
            {{ $cartItems->count() }} {{ Str::plural('item', $cartItems->count()) }}
        </p>

        @if ($cartItems->isNotEmpty())

            <div class="grid lg:grid-cols-3 gap-10">

                {{-- ── Items ── --}}
                <div class="lg:col-span-2 space-y-4">

                    @foreach ($cartItems as $item)
                        <div class="bg-white rounded-2xl p-5 flex gap-5 shadow-sm">

                            {{-- Image --}}
                            <div class="w-24 h-28 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                @php $img = $item->variant->product->images->first(); @endphp
                                @if ($img)
                                    <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover"
                                        alt="{{ $item->variant->product->name }}">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-medium text-[#1C1C1C] text-sm">
                                            {{ $item->variant->product->name }}
                                        </h3>
                                        <p class="text-gray-400 text-xs mt-1">
                                            Size: {{ $item->variant->size->name }}
                                            · Color: {{ ucfirst($item->variant->color->name) }}
                                        </p>
                                        <p class="text-gray-300 text-xs mt-0.5">
                                            SKU: {{ $item->variant->sku }}
                                        </p>
                                    </div>

                                    {{-- Remove --}}
                                    <form action="{{ route('cart.destroy', ['cartItem' => $item]) }}" method="POST">
                                        {{--  --}}
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-gray-300 hover:text-rose-500 transition-colors p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="flex items-center justify-between mt-4">

                                    {{-- Quantity stepper --}}
                                    <div class="flex items-center border border-gray-200 rounded-full">

                                        {{-- Decrease --}}
                                        <form action="{{ route('cart.update', ['cartItem' => $item]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity"
                                                value="{{ max(1, $item->quantity - 1) }}">
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500
                                               hover:text-black transition text-lg"
                                                @if ($item->quantity <= 1) disabled @endif>
                                                −
                                            </button>
                                        </form>

                                        <span class="w-8 text-center text-sm font-medium select-none">
                                            {{ $item->quantity }}
                                        </span>

                                        {{-- Increase --}}
                                        <form action="{{ route('cart.update', ['cartItem' => $item]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500
                                               hover:text-black transition text-lg"
                                                @if ($item->quantity >= $item->variant->stock_quantity) disabled @endif>
                                                +
                                            </button>
                                        </form>

                                    </div>

                                    <p class="font-semibold text-sm">
                                        ${{ number_format($item->line_total, 2) }}
                                    </p>
                                </div>

                                {{-- Low stock warning --}}
                                @if ($item->variant->stock_quantity <= 3)
                                    <p class="text-amber-500 text-xs mt-2">
                                        Only {{ $item->variant->stock_quantity }} left in stock
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Clear cart --}}
                    <form action="{{ route('cart.clear') }}" method="POST" class="inline-block">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-gray-400 hover:text-rose-500 transition-colors mt-1">
                            Clear bag
                        </button>
                    </form>

                    <a href="/"
                        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#C85C6E] transition-colors ml-4 mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Continue Shopping
                    </a>
                </div>

                {{-- ── Order Summary ── --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
                        <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-6">Summary</h2>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Subtotal</span>
                                <span>${{ number_format($cartTotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Shipping</span>
                                @if ($cartTotal >= $free_shipping_threshold)
                                    <span class="text-emerald-600 font-medium">Free</span>
                                @else
                                    <span>${{ $standardShippingMethod->price }}</span>
                                @endif
                            </div>

                            @if ($cartTotal < $free_shipping_threshold)
                                <p class="text-xs text-gray-400 bg-gray-50 p-3 rounded-lg">
                                    Add ${{ number_format($free_shipping_threshold - $cartTotal, 2) }} more for free
                                    shipping
                                </p>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 my-5"></div>

                        <div class="flex justify-between font-semibold text-lg mb-6">
                            <span>Total</span>
                            <span>
                                ${{ number_format($cartTotal + ($cartTotal < $free_shipping_threshold ? $standardShippingMethod->price : 0), 2) }}
                            </span>
                        </div>

                        @auth
                            <a href="{{ route('checkout.index') }}"
                                class="block w-full bg-[#1C1C1C] text-white text-center py-4 rounded-full
                                                                                                                                      font-medium hover:bg-[#C85C6E] transition-colors duration-300 text-sm">
                                Proceed to Checkout
                            </a>
                        @else
                            <div class="space-y-2">
                                <a href="{{ route('login') }}"
                                    class="block w-full bg-[#1C1C1C] text-white text-center py-4 rounded-full
                                                                                                                                          font-medium hover:bg-[#C85C6E] transition-colors duration-300 text-sm">
                                    Sign In to Checkout
                                </a>
                                <a href="{{ route('register') }}"
                                    class="block w-full border border-gray-200 text-center py-3.5 rounded-full
                                                                                                                                          text-sm text-gray-600 hover:border-gray-400 transition-colors">
                                    Create an Account
                                </a>
                                <p class="text-xs text-gray-400 text-center">
                                    Your bag is saved — sign in to complete your order
                                </p>
                            </div>
                        @endauth
                    </div>
                </div>

            </div>
        @else
            {{-- Empty state --}}
            <div class="text-center py-24">
                <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 class="font-['Cormorant_Garamond'] text-3xl font-light text-gray-500 mb-3">
                    Your bag is empty
                </h2>
                <p class="text-gray-400 font-light mb-8">Time to find something you love.</p>
                <a href="/"
                    class="inline-block bg-[#1C1C1C] text-white px-8 py-3.5 rounded-full
                                                                      hover:bg-[#C85C6E] transition-colors text-sm">
                    Start Shopping
                </a>
            </div>

        @endif
    </div>
</x-layout>
