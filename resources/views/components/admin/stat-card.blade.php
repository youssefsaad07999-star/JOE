{{--
  STAT CARD component
  Usage:
  <x-admin.stat-card
      label="Total Revenue"
      value="$12,430"
      change="+12%"
      trend="up"
      icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 6v1m0 4V12m0 0c-1.11 0-2.08-.402-2.599-1"
      color="rose"
  />
--}}

@props([
    'label',
    'value',
    'change' => null,
    'trend' => null, // 'up' | 'down' | null
    'icon',
    'color' => 'rose', // rose | blue | emerald | amber
])

@php
    $colors = [
        'rose' => 'bg-rose-100 text-[#C85C6E]',
        'blue' => 'bg-blue-100 text-blue-600',
        'emerald' => 'bg-emerald-100 text-emerald-600',
        'amber' => 'bg-amber-100 text-amber-600',
    ];
    $iconBg = $colors[$color] ?? $colors['rose'];
@endphp

<div class="bg-white rounded-2xl p-5 shadow-sm">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</p>
            <p class="text-2xl font-semibold mt-1.5 font-['sans-serif']">{{ $value }}</p>

            @if ($change)
                <p
                    class="text-xs mt-1.5 flex items-center gap-1
                           {{ $trend === 'up' ? 'text-emerald-600' : 'text-red-500' }}">
                    @if ($trend === 'up')
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                    {{ $change }} vs last month
                </p>
            @endif
        </div>

        <div class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center flex-shrink-0 ml-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}" />
            </svg>
        </div>
    </div>
</div>
