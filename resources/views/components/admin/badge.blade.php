@props(['status', 'type' => 'order'])

@php
    $orderColors = [
        'pending' => 'bg-amber-100 text-amber-700',
        'processing' => 'bg-blue-100 text-blue-700',
        'shipped' => 'bg-indigo-100 text-indigo-700',
        'delivered' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];

    $paymentColors = [
        'pending' => 'bg-amber-100 text-amber-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'failed' => 'bg-red-100 text-red-700',
        'refunded' => 'bg-purple-100 text-purple-700',
        'pending_cod' => 'bg-gray-100 text-gray-700',
        'cash on delivery' => 'bg-gray-100 text-emerald-700',
    ];

    $roleColors = [
        'admin' => 'bg-[#C85C6E]/10 text-[#C85C6E]',
        'customer' => 'bg-gray-100 text-gray-600',
    ];

    $map = match ($type) {
        'payment' => $paymentColors,
        'role' => $roleColors,
        default => $orderColors,
    };

    $class = $map[$status] ?? 'bg-gray-100 text-gray-600';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>
