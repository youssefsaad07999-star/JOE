<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} — JOE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F7F3EE] font-['DM_Sans'] text-[#1C1C1C]" x-data="{ sidebarOpen: localStorage.getItem('adminSidebar') !== 'false' }" x-init="$watch('sidebarOpen', v => localStorage.setItem('adminSidebar', v))">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-20 lg:hidden"
        style="display:none;">
    </div>

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <x-admin.nav />

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- TOP BAR --}}
            <header
                class="h-14 bg-white border-b border-gray-100 flex items-center justify-between px-6 flex-shrink-0 z-10">
                <div class="flex items-center gap-4">
                    {{-- Sidebar toggle --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Breadcrumb --}}
                    @isset($breadcrumb)
                        <nav class="flex items-center gap-2 text-sm text-gray-500">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#C85C6E] transition-colors">
                                Admin
                            </a>
                            @foreach ($breadcrumb as $crumb)
                                <span class="text-gray-300">/</span>
                                @if ($loop->last)
                                    <span class="text-[#1C1C1C] font-medium">{{ $crumb['label'] }}</span>
                                @else
                                    <a href="{{ $crumb['url'] }}"
                                        class="hover:text-[#C85C6E] transition-colors">{{ $crumb['label'] }}</a>
                                @endif
                            @endforeach
                        </nav>
                    @endisset
                </div>

                <div class="flex items-center gap-3">
                    <a href="/" target="_blank"
                        class="text-xs text-gray-400 hover:text-[#C85C6E] transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        View Store
                    </a>
                    <div
                        class="w-8 h-8 rounded-full bg-[#C85C6E] flex items-center justify-center text-white text-xs font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{-- Page header --}}
                @isset($heading)
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="font-['Cormorant_Garamond'] text-3xl font-semibold">{{ $heading }}</h1>
                            @isset($subheading)
                                <p class="text-gray-500 text-sm mt-0.5">{{ $subheading }}</p>
                            @endisset
                        </div>
                        @isset($actions)
                            <div class="flex items-center gap-3">{{ $actions }}</div>
                        @endisset
                    </div>
                @endisset

                {{-- Flash messages --}}
                @session('success')
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
                        class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700
                                rounded-xl px-4 py-3 text-sm flex items-center gap-2"
                        style="display:none;">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $value }}
                    </div>
                @endsession

                @session('error')
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                        class="mb-5 bg-red-50 border border-red-200 text-red-700
                                rounded-xl px-4 py-3 text-sm flex items-center gap-2"
                        style="display:none;">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $value }}
                    </div>
                @endsession

                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
