<x-admin.layout>
    <x-slot:heading>Customers</x-slot:heading>
    <x-slot:subheading>{{ $users->total() }} registered users</x-slot:subheading>

    <x-slot:actions>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..."
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-56
                          focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
            <select name="role"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30"
                onchange="this.form.submit()">
                <option value="">All roles</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <button type="submit"
                class="bg-[#1C1C1C] text-white px-4 py-2 rounded-xl text-sm hover:bg-[#C85C6E] transition-colors">
                Filter
            </button>
            @if (request()->anyFilled(['search', 'role']))
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-[#C85C6E]">Clear</a>
            @endif
        </form>
        {{-- search not working --}}
    </x-slot:actions>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">User
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Role
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Orders
                    </th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Spent
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Joined
                    </th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-[#C85C6E]/10 flex items-center
                                            justify-center text-[#C85C6E] font-semibold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium">
                                        {{ ucfirst($user->first_name) . ' ' . $user->last_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <x-admin.badge :status="$user->role ?? 'customer'" type="role" />
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $user->orders->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-right">
                            ${{ number_format($user->orders_sum_total_price ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="text-xs text-gray-500 hover:text-[#C85C6E] transition-colors">
                                    View
                                </a>

                                {{-- Toggle admin role --}}
                                @if ($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.role', $user) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="role"
                                            value="{{ $user->role === 'admin' ? 'customer' : 'admin' }}">
                                        <button type="submit"
                                            class="text-xs {{ $user->role === 'admin' ? 'text-amber-500 hover:text-[#C85C6E]' : 'text-gray-400 hover:text-[#C85C6E]' }} transition-colors">
                                            {{ $user->role === 'admin' ? 'Revoke Admin' : 'Make Admin' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400 text-sm">
                            No users found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>
