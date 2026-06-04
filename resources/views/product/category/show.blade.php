<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-10">

        {{-- Breadcrumb --}}
        <x-breadcrumb :items="[
            ['label' => ucfirst($gender->slug), 'url' => route('gender.index', ['gender' => $gender->slug])],
            ['label' => $category->name, 'url' => null],
        ]" />

        {{-- Header --}}
        <div class="mt-8 mb-10">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">
                {{ $gender->name }}
            </p>
            <h1 class="font-['Cormorant_Garamond'] text-5xl md:text-6xl font-light">{{ $category->name }}</h1>
        </div>

        {{-- Subcategory Pills --}}
        <div class="flex flex-wrap gap-3 mb-10 pb-8 border-b border-gray-200">
            <a href="{{ route('gender.category.show', ['gender' => $gender->slug, 'category' => $category->slug]) }}"
                class="px-5 py-2 rounded-full text-sm font-medium border transition-all duration-200
                      {{ !request('sub') ? 'bg-[#1C1C1C] text-white border-[#1C1C1C]' : 'border-gray-200 text-gray-600 hover:border-[#C85C6E] hover:text-[#C85C6E]' }}">
                All {{ $category->name }}
            </a>
            @foreach ($subcategories as $subcategory)
                <a href="{{ route('gender.subcategory.show', ['gender' => $gender->slug, 'category' => $category->slug, 'subcategory' => $subcategory->slug]) }}"
                    class="px-5 py-2 rounded-full text-sm font-medium border transition-all duration-200
                                                                                                                                                                                                                                          {{ request()->routeIs('*subcategory*') && request()->route('subcategory')?->id === $subcategory->id
                                                                                                                                                                                                                                              ? 'bg-[#C85C6E] text-white border-[#C85C6E]'
                                                                                                                                                                                                                                              : 'border-gray-200 text-gray-600 hover:border-[#C85C6E] hover:text-[#C85C6E]' }}">
                    {{ $subcategory->name }}
                </a>
            @endforeach
        </div>

        {{-- Filter & Sort --}}
        <div class="flex items-center justify-between mb-6">
            <p class="text-gray-500 text-sm">
                {{ count($products) }} products
            </p>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 text-sm border border-gray-200 rounded-full px-4 py-2 hover:border-gray-400 transition-colors">
                    Sort by
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false"
                    class="absolute right-0 top-full mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-10"
                    style="display:none;">
                    @foreach (['newest' => 'Newest First', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low'] as $val => $label)
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}"
                            class="block px-4 py-2 text-sm hover:bg-gray-50 {{ request('sort') === $val ? 'text-[#C85C6E] font-medium' : 'text-gray-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        @forelse($products as $product)
            @if ($loop->first)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @endif
            <x-product-card :product="$product" :gender="$gender" :category="$product->category->parent" :subcategory="$product->category" />
            @if ($loop->last)
    </div>
    @endif
@empty
    <div class="text-center py-24">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <p class="text-gray-400 font-light">No products in this category yet.</p>
        <a href="{{ route('gender.index', ['gender' => $gender->slug]) }}"
            class="inline-block mt-4 text-sm text-[#C85C6E] hover:underline">
            Browse all {{ $gender->name }}'s items
        </a>
    </div>
    @endforelse

    @if (isset($products) && method_exists($products, 'links'))
        <div class="mt-10">{{ $products->links() }}</div>
    @endif
    </div>
</x-layout>
