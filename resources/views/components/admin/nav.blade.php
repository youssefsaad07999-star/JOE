<aside
    class="w-64 bg-[#1C1C1C] text-white flex flex-col shrink-0 z-30
              fixed inset-y-0 left-0 lg:relative lg:translate-x-0
              transition-transform duration-300"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-0 lg:overflow-hidden'">

    {{-- Logo --}}
    <div class="h-14 flex items-center px-6 border-b border-white/10 shrink-0">
        <a href="{{ route('admin.dashboard') }}"
            class="font-['Cormorant_Garamond'] text-xl font-semibold tracking-widest hover:text-[#C85C6E] transition-colors">
            JOE
        </a>
        <span
            class="ml-2 text-[10px] font-semibold tracking-[0.15em] uppercase
                     bg-[#C85C6E] text-white px-1.5 py-0.5 rounded">
            Admin
        </span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3">

        @php
            $navItems = [
                [
                    'label' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'icon' =>
                        'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                ],
            ];
        @endphp

        {{-- Main --}}
        @foreach ($navItems as $item)
            <a href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-light transition-all mb-1
                      {{ request()->routeIs($item['route'])
                          ? 'bg-[#C85C6E] text-white'
                          : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- Catalog --}}
        <div class="mt-5 mb-2 px-3">
            <p class="text-[10px] font-semibold tracking-[0.15em] uppercase text-gray-600">Catalog</p>
        </div>

        @php
            $catalogItems = [
                [
                    'label' => 'Products',
                    'route' => 'admin.products.index',
                    'match' => 'admin.products.*',
                    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                ],
                [
                    'label' => 'Categories',
                    'route' => 'admin.categories.index',
                    'match' => 'admin.categories.*',
                    'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                ],
            ];
        @endphp

        @foreach ($catalogItems as $item)
            <a href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-light transition-all mb-1
                      {{ request()->routeIs($item['match'])
                          ? 'bg-[#C85C6E] text-white'
                          : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- Sales --}}
        <div class="mt-5 mb-2 px-3">
            <p class="text-[10px] font-semibold tracking-[0.15em] uppercase text-gray-600">Sales</p>
        </div>

        @php
            $salesItems = [
                [
                    'label' => 'Orders',
                    'route' => 'admin.orders.index',
                    'match' => 'admin.orders.*',
                    'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                ],
                [
                    'label' => 'Customers',
                    'route' => 'admin.users.index',
                    'match' => 'admin.users.*',
                    'icon' =>
                        'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                ],
            ];
        @endphp

        @foreach ($salesItems as $item)
            <a href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-light transition-all mb-1
                      {{ request()->routeIs($item['match'])
                          ? 'bg-[#C85C6E] text-white'
                          : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                </svg>

                {{-- Order badge --}}
                @if ($item['label'] === 'Orders')
                    {{ $item['label'] }}
                    @if ($pendingCount > 0)
                        <span
                            class="ml-auto bg-[#C85C6E] text-white text-[10px] font-bold
                                     px-1.5 py-0.5 rounded-full min-w-4.5 text-center">
                            {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                        </span>
                    @endif
                @else
                    {{ $item['label'] }}
                @endif
            </a>
        @endforeach

        {{-- Settings --}}
        <div class="mt-5 mb-2 px-3">
            <p class="text-[10px] font-semibold tracking-[0.15em] uppercase text-gray-600">Settings</p>
        </div>

        <a href="{{ route('admin.shipping.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-light transition-all mb-1
                  {{ request()->routeIs('admin.shipping.*')
                      ? 'bg-[#C85C6E] text-white'
                      : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            Shipping
        </a>
    </nav>

    {{-- User footer --}}
    <div class="border-t border-white/10 p-4 shrink-0">
        <div class="flex items-center gap-3">
            <div
                class="w-8 h-8 rounded-full bg-[#C85C6E] flex items-center justify-center
                        text-white text-xs font-semibold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="p-1.5 text-gray-500 hover:text-white transition-colors rounded-lg hover:bg-white/10"
                    title="Sign out">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
