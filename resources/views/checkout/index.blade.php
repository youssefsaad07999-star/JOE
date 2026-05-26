<x-layout>
    <div class="max-w-6xl mx-auto px-6 py-10">

        {{-- Progress Steps --}}
        <div class="flex items-center justify-center gap-0 mb-12">
            @foreach(['Cart', 'Details', 'Payment', 'Confirm'] as $i => $step)
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold
                                                                                                                {{ $i <= 1 ? 'bg-[#1C1C1C] text-white' : 'bg-gray-200 text-gray-400' }}">
                            {{ $i < 1 ? '✓' : $i + 1 }}
                        </div>
                        <span class="text-xs mt-1 {{ $i <= 1 ? 'text-[#1C1C1C] font-medium' : 'text-gray-400' }}">
                            {{ $step }}
                        </span>
                    </div>
                    @if(!$loop->last)
                        <div class="w-16 md:w-24 h-px {{ $i < 1 ? 'bg-[#1C1C1C]' : 'bg-gray-200' }} mx-2 mb-4"></div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="grid lg:grid-cols-5 gap-12">

            {{-- Checkout Form --}}
            <div class="lg:col-span-3">
                <form action="" method="POST" class="space-y-8">
                    {{-- {{ route('checkout.store') }} --}}
                    @csrf

                    {{-- Contact --}}
                    <div>
                        <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-5">Contact Information</h2>
                        <div class="space-y-4">
                            <x-form.field name="email" title="Email Address" type="email" placeholder="you@example.com"
                                :value="auth()->user()?->email" />
                            <x-form.field name="phone" title="Phone Number" type="tel" placeholder="+1 234 567 8900"
                                :value="auth()->user()?->phone_number" />
                        </div>
                    </div>

                    {{-- Shipping --}}
                    <div>
                        <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-5">Shipping Address</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <x-form.field name="first_name" title="First Name" placeholder="John"
                                    :value="auth()->user()?->name ? explode(' ', auth()->user()->name)[0] : ''" />
                                <x-form.field name="last_name" title="Last Name" placeholder="Doe"
                                    :value="auth()->user()?->name ? explode(' ', auth()->user()->name)[1] ? explode(' ', auth()->user()->name)[1] : '' : ''" />
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
                                    @foreach($countries as $country)
                                        <option value="{{ $country->Code }}">{{ $country->name }}</option>
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
                            @foreach($shipping_methods as $method)
                                <label
                                    class="flex items-center justify-between p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-[#C85C6E] transition-colors has-[:checked]:border-[#C85C6E] has-[:checked]:bg-rose-50">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="shipping_method" value="{{ $method->id }}" {{ $loop->first ? 'checked' : '' }}>
                                        <div>
                                            <p class="text-sm font-medium">{{ $method->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $method->delivery_time }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold">
                                        {{ $cartTotal >= $free_shipping_threshold && $method->name === 'Standard Shipping' ? 'Free' : '$' . number_format($method->price, 2) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>


                    {{-- Payment --}}
                    <div>
                        <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-5">Payment</h2>
                        <div x-data="{ method: 'card' }">
                            <div class="flex gap-3 mb-5">
                                <button type="button" @click="method = 'card'"
                                    :class="method === 'card' ? 'border-[#1C1C1C] bg-[#1C1C1C] text-white' : 'border-gray-200 text-gray-600'"
                                    class="flex-1 py-2.5 rounded-xl border text-sm font-medium transition-all">
                                    Credit Card
                                </button>
                                <button type="button" @click="method = 'paypal'"
                                    :class="method === 'paypal' ? 'border-[#1C1C1C] bg-[#1C1C1C] text-white' : 'border-gray-200 text-gray-600'"
                                    class="flex-1 py-2.5 rounded-xl border text-sm font-medium transition-all">
                                    PayPal
                                </button>
                                <button type="button" @click="method = 'cod'"
                                    :class="method === 'cod' ? 'border-[#1C1C1C] bg-[#1C1C1C] text-white' : 'border-gray-200 text-gray-600'"
                                    class="flex-1 py-2.5 rounded-xl border text-sm font-medium transition-all">
                                    Cash on Delivery
                                </button>
                            </div>

                            <div x-show="method === 'card'" class="space-y-4">
                                <x-form.field name="card_name" title="Name on Card" placeholder="John Doe" />
                                <x-form.field name="card_number" title="Card Number"
                                    placeholder="1234 5678 9012 3456" />
                                <div class="grid grid-cols-2 gap-4">
                                    <x-form.field name="card_expiry" title="Expiry Date" placeholder="MM / YY" />
                                    <x-form.field name="card_cvv" title="CVV" placeholder="123" />
                                </div>
                            </div>
                            <div x-show="method === 'paypal'" class="text-center py-8 text-gray-500 text-sm"
                                style="display:none;">
                                You'll be redirected to PayPal to complete your payment.
                            </div>
                            <div x-show="method === 'cod'"
                                class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700"
                                style="display:none;">
                                Pay when your order is delivered. Available for select locations only.
                            </div>

                            <input type="hidden" name="payment_method" :value="method">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#1C1C1C] text-white py-4 rounded-full font-medium
                                   hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide">
                        Place Order · ${{ number_format($cartTotal, 2) }}
                    </button>

                    <p class="text-center text-xs text-gray-400">
                        By placing your order, you agree to our
                        <a href="#" class="underline">Terms</a> and
                        <a href="#" class="underline">Privacy Policy</a>
                    </p>
                </form>
            </div>

            {{-- Order Review --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
                    <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold mb-5">Your Order</h2>

                    <div class="space-y-4 max-h-72 overflow-y-auto pr-1">
                        @foreach($cartItems as $item)
                            <div class="flex gap-3">
                                <div class="w-16 h-20 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 relative">
                                    @if($item->variant->image)
                                        <img src="{{ asset('storage/' . $item->variant->image) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200"></div>
                                    @endif
                                    <span
                                        class="absolute -top-1 -right-1 bg-[#1C1C1C] text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold">
                                        {{ $item->quantity }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item->variant->product->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $item->size ?? '' }}{{ $item->color ? ' · ' . $item->color : '' }}
                                    </p>
                                    <p class="text-sm font-semibold mt-1">
                                        ${{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 mt-5 pt-5 space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span>${{ number_format($cartTotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Shipping</span>
                            <span class="{{ $cartTotal >= $free_shipping_threshold ? 'text-emerald-600' : '' }}">
                                {{ $cartTotal >= $free_shipping_threshold ? 'Free' : $shipping_methods->first()->price }}
                            </span>
                        </div>
                        <div class="flex justify-between font-semibold text-base border-t border-gray-100 pt-3 mt-3">
                            <span>Total</span>
                            <span>${{ number_format($cartTotal + ($cartTotal < $free_shipping_threshold ? $shipping_methods->first()->price : 0), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layout>