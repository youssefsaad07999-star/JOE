<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'JOE — Fashion for Every You' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F7F3EE] text-[#1C1C1C] flex flex-col min-h-screen font-['DM_Sans']"
  x-data="{ cartOpen: false, mobileMenuOpen: false }">

  {{-- CART SIDEBAR OVERLAY --}}
  <div x-show="cartOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="cartOpen = false"
    class="fixed inset-0 bg-black/50 z-40" style="display:none;">
  </div>

  {{-- CART SIDEBAR --}}
  <div x-show="cartOpen" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed right-0 top-0 h-full w-full max-w-sm bg-white z-50 flex flex-col shadow-2xl" style="display:none;">
    {{-- Cart Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
      <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold">Your Cart</h2>
      <button @click="cartOpen = false" class="p-2 hover:bg-gray-100 rounded-full transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    {{-- Cart Items --}}
    <div class="flex-1 overflow-y-auto px-6 py-4">
      @if(isset($cartItems) && $cartItems->count() > 0)
        @foreach($cartItems as $item)
          <div class="flex gap-4 py-4 border-b border-gray-100">
            <div class="w-20 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
              @if($item->product->image)
                <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover"
                  alt="{{ $item->product->name }}">
              @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                  <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
              @endif
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-sm truncate">{{ $item->product->name }}</p>
              <p class="text-gray-500 text-xs mt-0.5">{{ $item->size ?? 'One Size' }} · {{ $item->color ?? '' }}</p>
              <div class="flex items-center justify-between mt-2">
                <div class="flex items-center border border-gray-200 rounded-full">
                  <form action="{{ route('cart.decrease', $item) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                      class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-black transition">−</button>
                  </form>
                  <span class="w-6 text-center text-sm">{{ $item->quantity }}</span>
                  <form action="{{ route('cart.increase', $item) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                      class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-black transition">+</button>
                  </form>
                </div>
                <p class="font-semibold text-sm">${{ number_format($item->product->price * $item->quantity, 2) }}</p>
              </div>
            </div>
            <form action="{{ route('cart.remove', $item) }}" method="POST">
              @csrf @method('DELETE')
              <button type="submit" class="text-gray-300 hover:text-rose-500 transition mt-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </form>
          </div>
        @endforeach
      @else
        <div class="flex flex-col items-center justify-center h-full text-center py-16">
          <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
          <p class="text-gray-400 font-light">Your cart is empty</p>
          <a href="/" @click="cartOpen = false" class="mt-4 text-sm text-[#C85C6E] hover:underline">Continue Shopping</a>
        </div>
      @endif
    </div>

    {{-- Cart Footer --}}
    @if(isset($cartItems) && $cartItems->count() > 0)
      <div class="px-6 py-5 border-t border-gray-100 bg-gray-50">
        <div class="flex justify-between items-center mb-4">
          <span class="text-gray-600">Subtotal</span>
          <span class="font-semibold text-lg">${{ number_format($cartTotal ?? 0, 2) }}</span>
        </div>
        <a href="{{ route('checkout.index') }}"
          class="block w-full bg-[#1C1C1C] text-white text-center py-3.5 rounded-full font-medium hover:bg-[#C85C6E] transition-colors duration-300">
          Proceed to Checkout
        </a>
        <a href="{{ route('cart.index') }}"
          class="block w-full text-center py-2.5 mt-2 text-sm text-gray-500 hover:text-black transition">
          View Full Cart
        </a>
      </div>
    @endif
  </div>

  {{-- NAV --}}
  <x-layout.nav />

  {{-- MAIN CONTENT --}}
  <main class="flex-grow">
    {{ $slot }}
  </main>

  {{-- FOOTER --}}
  <x-layout.footer />

  {{-- SESSION FLASH --}}
  @session('success')
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3500)" x-show="show"
      x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
      x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
      class="fixed bottom-6 right-6 bg-[#1C1C1C] text-white px-5 py-3.5 rounded-2xl shadow-xl text-sm font-medium z-50 flex items-center gap-3"
      style="display:none;">
      <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd"
          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
          clip-rule="evenodd" />
      </svg>
      {{ $value }}
    </div>
  @endsession

  @session('error')
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
      class="fixed bottom-6 right-6 bg-rose-600 text-white px-5 py-3.5 rounded-2xl shadow-xl text-sm font-medium z-50 flex items-center gap-3"
      style="display:none;">
      <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd"
          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
          clip-rule="evenodd" />
      </svg>
      {{ $value }}
    </div>
  @endsession
</body>

</html>