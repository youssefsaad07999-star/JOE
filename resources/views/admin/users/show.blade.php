<x-admin.layout>
    <x-slot:breadcrumb>
        [['label' => 'Customers', 'url' => route('admin.users.index')],
        ['label' => ucfirst($user->first_name) . ' ' . ucfirst($user->last_name), 'url' => null]]
    </x-slot:breadcrumb>
    <x-slot:heading>{{ ucfirst($user->first_name) . ' ' . ucfirst($user->last_name) }}</x-slot:heading>
    <x-slot:subheading>{{ $user->email }}</x-slot:subheading>

    <x-slot:actions>
        @if ($user->id !== auth()->id())
            <form action="{{ route('admin.users.role', $user) }}" method="POST" class="w-full sm:w-auto">
                @csrf @method('PATCH')
                <input type="hidden" name="role" value="{{ $user->role === 'admin' ? 'customer' : 'admin' }}">
                <button type="submit"
                    class="w-full sm:w-auto text-center border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm
                               hover:border-[#C85C6E] hover:text-[#C85C6E] transition-colors">
                    {{ $user->role === 'admin' ? 'Revoke Admin' : 'Make Admin' }}
                </button>
            </form>
        @endif
    </x-slot:actions>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Orders list --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold">
                        Orders
                        <span class="text-sm font-light text-gray-400 ml-2">{{ $user->orders->count() }} total</span>
                    </h2>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full min-w-[540px]">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th
                                    class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    Order</th>
                                <th
                                    class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    Status</th>
                                <th
                                    class="text-right px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    Total</th>
                                <th
                                    class="text-right px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="text-sm font-medium hover:text-[#C85C6E] transition-colors">
                                            #{{ $order->id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <x-admin.badge :status="$order->status" />
                                    </td>
                                    <td class="px-6 py-3 text-sm font-semibold text-right whitespace-nowrap">
                                        ${{ number_format($order->total_price, 2) }}
                                    </td>
                                    <td class="px-6 py-3 text-xs text-gray-400 text-right whitespace-nowrap">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-sm">
                                        No orders yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- User Info --}}
        <div class="space-y-4">

            <div class="bg-white rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-sm mb-4">Profile</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Name</span>
                        <span
                            class="text-right">{{ ucfirst($user->first_name) . ' ' . ucfirst($user->last_name) }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Email</span>
                        <span class="truncate text-right max-w-[180px] sm:max-w-none"
                            title="{{ $user->email }}">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Phone</span>
                        <span class="text-right">{{ $user->phone_number ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Date of Birth</span>
                        <span class="text-right">{{ $user->date_of_birth?->format('M d, Y') ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Role</span>
                        <x-admin.badge :status="$user->role ?? 'customer'" type="role" />
                    </div>
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Joined</span>
                        <span class="text-right">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-sm mb-4">Stats</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Total Orders</span>
                        <span class="font-semibold text-right">{{ $user->orders->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Total Spent</span>
                        <span class="font-semibold text-right">
                            ${{ number_format($user->orders->where('status', '!=', 'cancelled')->sum('total_price'), 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Avg Order</span>
                        <span class="font-semibold text-right">
                            @php
                                $completed = $user->orders->where('status', '!=', 'cancelled');
                                $avg = $completed->count() > 0 ? $completed->avg('total_price') : 0;
                            @endphp
                            ${{ number_format($avg, 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Addresses</span>
                        <span class="text-right">{{ $user->addresses->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Addresses --}}
            @if ($user->addresses->count())
                <div class="bg-white rounded-2xl p-5 shadow-sm">
                    <h3 class="font-semibold text-sm mb-3">Saved Addresses</h3>
                    <div class="space-y-3">
                        @foreach ($user->addresses as $address)
                            <div
                                class="text-xs text-gray-600 leading-relaxed
                                        {{ $address->is_default ? 'border-l-2 border-[#C85C6E] pl-3' : '' }}">
                                @if ($address->label)
                                    <p class="font-medium text-gray-700 mb-0.5">{{ $address->label }}</p>
                                @endif
                                <p>{{ $address->description }}</p>
                                <p>{{ $address->city }}, {{ $address->country }}</p>
                                @if ($address->is_default)
                                    <span class="text-[#C85C6E] text-[10px] font-semibold">Default</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-admin.layout>
