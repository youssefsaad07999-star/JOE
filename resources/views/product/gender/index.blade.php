<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-10">

        {{-- Breadcrumb --}}
        <x-breadcrumb :items="[['label' => ucfirst($gender->name), 'url' => null]]" />

        {{-- Header --}}
        <div class="mt-8 mb-2">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">Collection</p>
            <h1 class="font-['Cormorant_Garamond'] text-5xl md:text-6xl font-light">{{ $gender->name }}</h1>
        </div>

        {{-- Category Grid --}}
        <div class="flex gap-6 overflow-x-auto py-4 border-t border-gray-200">
            @foreach($categories as $category)
                <a href="{{ route('gender.category.show', ['gender' => $gender->slug, 'category' => $category->slug]) }}"
                    class="group text-sm font-light tracking-wide text-gray-700 hover:text-[#C85C6E] transition-colors flex items-center gap-1">
                    {{ $category->name }}
                    <svg class="w-3 h-3 opacity-50 group-hover:opacity-100 transition-opacity" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @endforeach
        </div>

        {{-- All Products --}}
        <div class="border-t border-gray-200 pt-10">
            <h2 class="font-['Cormorant_Garamond'] text-3xl font-light mb-8">All {{ $gender->name }}'s Products
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @forelse($products as $product)
                    <x-product-card :product="$product" :gender="$gender" :category="$product->category->parent"
                        :subcategory="$product->category" />
                @empty
                    <div class="col-span-full text-center py-20">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-400 font-light">No products yet — check back soon!</p>
                    </div>
                @endforelse
            </div>

            @if(isset($products) && method_exists($products, 'links'))
                <div class="mt-10">{{ $products->links() }}</div>
            @endif
        </div>

    </div>
</x-layout>