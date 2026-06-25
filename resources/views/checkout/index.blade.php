<x-layout>
    <div x-data="{
        cartTotal: {{ $cartTotal }},
        freeThreshold: {{ $free_shipping_threshold }},
        shippingPrice: {{ $cartTotal >= $free_shipping_threshold && $shipping_methods->first()->name === 'Standard Shipping' ? 0 : $shipping_methods->first()->price }}
    }" class="max-w-6xl mx-auto px-6 py-10">

        {{-- Progress Steps --}}
        <div class="flex items-center justify-center gap-0 mb-12">
            @foreach (['Cart', 'Details', 'Payment', 'Confirm'] as $i => $step)
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold {{ $i <= 1 ? 'bg-[#1C1C1C] text-white' : 'bg-gray-200 text-gray-400' }}">
                            {{ $i < 1 ? '✓' : $i + 1 }}
                        </div>
                        <span class="text-xs mt-1 {{ $i <= 1 ? 'text-[#1C1C1C] font-medium' : 'text-gray-400' }}">
                            {{ $step }}
                        </span>
                    </div>
                    @if (!$loop->last)
                        <div class="w-16 md:w-24 h-px {{ $i < 1 ? 'bg-[#1C1C1C]' : 'bg-gray-200' }} mx-2 mb-4"></div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="grid lg:grid-cols-5 gap-12">

            {{-- Checkout Form --}}
            <div class="lg:col-span-3">
                <form action="{{ route('checkout.store') }}" method="POST" class="space-y-8">
                    @csrf

                    {{-- Contact --}}
                    <div>
                        <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-5">Contact Information</h2>
                        <div class="space-y-4">
                            <x-form.field name="email" title="Email Address" type="email"
                                placeholder="you@example.com" :value="auth()->user()?->email" />
                            <x-form.field name="phone" title="Phone Number" type="tel"
                                placeholder="+1 234 567 8900" :value="auth()->user()?->phone_number" />
                        </div>
                    </div>

                    {{-- Shipping Address --}}
                    <div>
                        <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-5">Shipping Address</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <x-form.field name="first_name" title="First Name" placeholder="John"
                                    :value="auth()->user()?->first_name ?? ''" />
                                <x-form.field name="last_name" title="Last Name" placeholder="Doe" :value="auth()->user()?->last_name ?? ''" />
                            </div>
                            <x-form.field name="address" title="Street Address" placeholder="123 Fashion Street" />
                            <x-form.field name="address2" title="Apartment, suite, etc. (optional)"
                                placeholder="Apt 4B" />
                            <div class="grid grid-cols-2 gap-4">
                                <x-form.field name="city" title="City" placeholder="New York" />
                                <x-form.field name="postal_code" title="Postal Code" placeholder="10001" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Country</label>
                                <select name="country"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E] transition-colors">
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->code }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <x-form.error name="country" />
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Method --}}
                    <div>
                        <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-5">Shipping Method</h2>
                        <div class="space-y-3">
                            @foreach ($shipping_methods as $method)
                                @php
                                    $actualPrice =
                                        $cartTotal >= $free_shipping_threshold && $method->name === 'Standard Shipping'
                                            ? 0
                                            : $method->price;
                                @endphp
                                <label
                                    class="flex items-center justify-between p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-[#C85C6E] transition-colors has-[:checked]:border-[#C85C6E] has-[:checked]:bg-rose-50/40">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="shipping_method" value="{{ $method->id }}"
                                            class="text-[#C85C6E] focus:ring-[#C85C6E]"
                                            @click="shippingPrice = {{ $actualPrice }}"
                                            {{ $loop->first ? 'checked' : '' }}>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $method->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $method->delivery_time }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ $actualPrice == 0 ? 'Free' : '$' . number_format($actualPrice, 2) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-5">Payment</h2>
                        <div x-data="{ method: 'card' }">
                            <div class="flex gap-3 mb-5">
                                <label
                                    class="flex-1 text-center py-2.5 rounded-xl border text-sm font-medium transition-all cursor-pointer border-gray-200 text-gray-600 has-[:checked]:border-[#1C1C1C] has-[:checked]:bg-[#1C1C1C] has-[:checked]:text-white">
                                    <input type="radio" name="payment_method" value="card" class="sr-only"
                                        @change="method = 'card'" checked>
                                    Credit Card
                                </label>

                                <label
                                    class="flex-1 text-center py-2.5 rounded-xl border text-sm font-medium transition-all cursor-pointer border-gray-200 text-gray-600 has-[:checked]:border-[#1C1C1C] has-[:checked]:bg-[#1C1C1C] has-[:checked]:text-white">
                                    <input type="radio" name="payment_method" value="cod" class="sr-only"
                                        @change="method = 'cod'">
                                    Cash on Delivery
                                </label>
                            </div>

                            <div x-show="method === 'card'" x-transition
                                class="p-4 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-600">
                                You'll be redirected to Paddle's secure payment page to complete your card payment.
                            </div>

                            <div x-show="method === 'cod'" x-cloak x-transition
                                class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                                Pay when your order is delivered. Available for select locations only.
                            </div>
                        </div>
                    </div>

                    {{-- Action Trigger --}}
                    <div class="space-y-4">
                        <button type="submit"
                            class="w-full bg-[#1C1C1C] text-white py-4 rounded-full font-medium hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide cursor-pointer shadow-sm">
                            Place Order · $<span x-text="(cartTotal + shippingPrice).toFixed(2)"></span>
                        </button>

                        <p class="text-center text-xs text-gray-400">
                            By placing your order, you agree to our
                            <a href="#" class="underline hover:text-gray-600">Terms</a> and
                            <a href="#" class="underline hover:text-gray-600">Privacy Policy</a>
                        </p>
                    </div>
                </form>
            </div>

            {{-- Order Review Sidebar --}}
            <aside class="lg:col-span-2">
                <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24 border border-gray-100">
                    <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-5">Your Order</h2>

                    <div class="space-y-4 max-h-72 overflow-y-auto pr-1">
                        @foreach ($cartItems as $item)
                            <div class="flex gap-3">
                                <div class="w-16 h-20 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 relative">
                                    @php
                                        $img = $item->variant->product->images
                                            ->where('color_id', $item->variant->color_id)
                                            ->first();
                                    @endphp
                                    @if ($img)
                                        <img src="{{ asset('storage/' . $img->image_path) }}"
                                            class="w-full h-full object-cover"
                                            alt="{{ $item->variant->product->name }}">
                                    @else
                                        <div class="w-full h-full bg-gray-200"></div>
                                    @endif
                                    <span
                                        class="absolute -top-1 -right-1 bg-[#1C1C1C] text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold">
                                        {{ $item->quantity }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate text-gray-900">
                                        {{ $item->variant->product->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $item->variant->size->name ?? '' }}
                                        {{ $item->variant->color ? ' · ' . ucfirst($item->variant->color->name) : '' }}
                                    </p>
                                    <p class="text-sm font-semibold mt-1 text-gray-900">
                                        ${{ number_format($item->unit_price * $item->quantity, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 mt-5 pt-5 space-y-2.5 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">${{ number_format($cartTotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span
                                :class="shippingPrice === 0 ? 'text-emerald-600 font-medium' : 'text-gray-900 font-medium'"
                                x-text="shippingPrice === 0 ? 'Free' : '$' + shippingPrice.toFixed(2)">
                            </span>
                        </div>

                        <div
                            class="flex justify-between font-semibold text-base border-t border-gray-100 pt-3 mt-3 text-gray-900">
                            <span>Total</span>
                            <span>$<span x-text="(cartTotal + shippingPrice).toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</x-layout>
