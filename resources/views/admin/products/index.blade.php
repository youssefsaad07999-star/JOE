<x-admin.layout>
    <x-slot:heading>Products</x-slot:heading>
    <x-slot:subheading>{{ $products->total() }} products</x-slot:subheading>

    <x-slot:actions>
        {{-- Unified Responsive Actions Container --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 w-full md:w-auto">

            {{-- Filter Form: Columns on phones, inline rows on tablets and up --}}
            <form method="GET"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 md:flex-initial">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-full sm:w-52 bg-white shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="gender"
                        class="border border-gray-200 rounded-xl px-3 py-2 text-sm flex-1 sm:flex-initial bg-white shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]"
                        onchange="this.form.submit()">
                        <option value="">All genders</option>
                        @foreach ($genders as $gender)
                            <option value="{{ $gender->id }}"
                                {{ request('gender') == $gender->id ? 'selected' : '' }}>
                                {{ $gender->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="bg-[#1C1C1C] text-white px-4 py-2 rounded-xl text-sm hover:bg-[#C85C6E] transition-colors font-medium shadow-sm flex-1 sm:flex-initial text-center">
                        Filter
                    </button>
                </div>
            </form>

            {{-- Primary Creation Action Button --}}
            <a href="{{ route('admin.products.create') }}"
                class="bg-[#C85C6E] text-white px-4 py-2 rounded-xl text-sm font-medium
                       hover:bg-[#b54e60] transition-colors flex items-center justify-center gap-2 shadow-sm whitespace-nowrap w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Product
            </a>
        </div>
    </x-slot:actions>

    {{-- Safe Layout Product Card Container --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Scroll Wrapper: Prevents wide category paths from breaking the master panel grid --}}
        <div class="overflow-x-auto w-full min-w-full align-middle">
            <table class="w-full min-w-[950px] table-auto">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/70">
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Product</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Category</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Variants</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Stock</th>
                        <th class="text-right px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Price</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Status</th>
                        <th class="px-6 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                        @php
                            $totalStock = $product->variants_sum_stock_quantity ?? 0;
                            $img = $product->images->first();
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Product Detail Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-12 rounded-lg overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0 shadow-sm">
                                        @if ($img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                <span
                                                    class="text-[9px] text-gray-400 font-medium uppercase tracking-wider">No
                                                    Img</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate max-w-[220px]">
                                            {{ $product->name }}</p>
                                        <p
                                            class="text-xs text-gray-400 font-mono tracking-tight truncate max-w-[220px]">
                                            {{ $product->slug }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Nested Categories Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-700 font-medium">
                                    {{ $product->category->parent?->name ?? 'Root' }} <span
                                        class="text-gray-300 mx-0.5">/</span> {{ $product->category->name }}
                                </p>
                                @if ($product->category->parent?->parent?->name)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $product->category->parent->parent->name }}
                                    </p>
                                @endif
                            </td>

                            {{-- Variant Metric Column --}}
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap font-medium">
                                {{ $product->variants_count }} {{ Str::plural('variant', $product->variants_count) }}
                            </td>

                            {{-- Inventory Levels Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm px-2.5 py-0.5 rounded-full font-medium inline-block
                                    {{ $totalStock === 0 ? 'bg-red-50 text-red-700 border border-red-100' : ($totalStock <= 10 ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-gray-50 text-gray-700 border border-gray-100') }}">
                                    {{ $totalStock }} units
                                </span>
                            </td>

                            {{-- Price Column --}}
                            <td class="px-6 py-4 text-sm font-semibold text-right text-gray-900 whitespace-nowrap">
                                ${{ number_format($product->base_price, 2) }}
                            </td>

                            {{-- Status Toggle Switch Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('admin.products.update', $product) }}" method="POST"
                                    class="flex items-center">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="toggle_active" value="1">
                                    <button type="submit" aria-label="Toggle active state"
                                        class="relative inline-flex h-5 w-9 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30
                                               {{ $product->is_active ? 'bg-[#C85C6E]' : 'bg-gray-200' }}">
                                        <span
                                            class="inline-block w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 mt-0.5
                                                     {{ $product->is_active ? 'translate-x-4' : 'translate-x-0.5' }}">
                                        </span>
                                    </button>
                                </form>
                            </td>

                            {{-- Quick Execution Link Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3.5">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="text-xs text-gray-600 hover:text-[#C85C6E] bg-gray-50 hover:bg-[#C85C6E]/5 px-2.5 py-1 rounded-md border border-gray-100 font-medium transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                        x-data @submit.prevent="confirm('Delete this product?') && $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-xs text-red-500 hover:text-red-700 bg-red-50/50 hover:bg-red-50 px-2.5 py-1 rounded-md border border-red-100/50 font-medium transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-400 text-sm bg-gray-50/10">
                                <span>No products match your query.</span>
                                <a href="{{ route('admin.products.create') }}"
                                    class="text-[#C85C6E] hover:underline font-medium ml-1">
                                    Create one now
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Block Container --}}
        <x-paginator :paginator="$products" />
    </div>
</x-admin.layout>
