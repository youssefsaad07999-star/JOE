<x-admin.layout>
    <x-slot:heading>Products</x-slot:heading>
    <x-slot:subheading>{{ $products->total() }} products</x-slot:subheading>

    <x-slot:actions>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-52
                          focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">

            <select name="gender"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30"
                onchange="this.form.submit()">
                <option value="">All genders</option>
                @foreach ($genders as $gender)
                    <option value="{{ $gender->id }}" {{ request('gender') == $gender->id ? 'selected' : '' }}>
                        {{ $gender->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="bg-[#1C1C1C] text-white px-4 py-2 rounded-xl text-sm hover:bg-[#C85C6E] transition-colors">
                Filter
            </button>
        </form>

        <a href="{{ route('admin.products.create') }}"
            class="bg-[#C85C6E] text-white px-4 py-2 rounded-xl text-sm font-medium
                  hover:bg-[#b54e60] transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Product
        </a>
    </x-slot:actions>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Product
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Category
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Variants
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Stock
                    </th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Price
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status
                    </th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                    @php
                        $totalStock = $product->variants_sum_stock_quantity ?? 0;
                        $img = $product->images->first();
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if ($img)
                                        <img src="{{ asset('storage/' . $img->image_path) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200"></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $product->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">
                                {{ $product->category->parent?->name }} - {{ $product->category->name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $product->category->parent?->parent?->name }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $product->variants_count }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="text-sm {{ $totalStock === 0 ? 'text-red-600 font-semibold' : ($totalStock <= 10 ? 'text-amber-600 font-medium' : 'text-gray-600') }}">
                                {{ $totalStock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-right">
                            ${{ number_format($product->base_price, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.products.update', $product) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="toggle_active" value="1">
                                <button type="submit"
                                    class="relative inline-flex h-5 w-9 rounded-full transition-colors duration-200
                                               {{ $product->is_active ? 'bg-[#C85C6E]' : 'bg-gray-200' }}">
                                    <span
                                        class="inline-block w-4 h-4 bg-white rounded-full shadow transform
                                                 transition-transform duration-200 mt-0.5
                                                 {{ $product->is_active ? 'translate-x-4' : 'translate-x-0.5' }}">
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="text-xs text-gray-500 hover:text-[#C85C6E] transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" x-data
                                    @submit.prevent="confirm('Delete this product?') && $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-red-400 hover:text-red-600 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-gray-400 text-sm">
                            No products yet.
                            <a href="{{ route('admin.products.create') }}"
                                class="text-[#C85C6E] hover:underline ml-1">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>
