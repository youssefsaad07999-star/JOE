<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-10">

        {{-- Breadcrumb --}}
        <x-breadcrumb :items="[
            ['label' => $gender->name, 'url' => route('gender.index', ['gender' => $gender])],
            [
                'label' => $category->name,
                'url' => route('gender.category.show', ['gender' => $gender->slug, 'category' => $category->slug]),
            ],
            ['label' => $subcategory->name, 'url' => null],
        ]" />

        {{-- Header --}}
        <div class="mt-8 mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">
                    {{ $gender->name }} · {{ $category->name }}
                </p>
                <h1 class="font-['Cormorant_Garamond'] text-5xl md:text-6xl font-light">{{ $subcategory->name }}</h1>
            </div>

            {{-- Other Subcategories --}}
            <div class="flex flex-wrap gap-2">
                @foreach ($subcategories as $sub)
                    <a href="{{ route('gender.subcategory.show', [
                        'gender' => $gender->slug,
                        'category' => $category->slug,
                        'subcategory' => $sub->slug,
                    ]) }}"
                        class="px-4 py-1.5 rounded-full text-xs font-medium border transition-all duration-200
                                                                                                                                                                                                                  {{ $sub->id === $subcategory->id
                                                                                                                                                                                                                      ? 'bg-[#C85C6E] text-white border-[#C85C6E]'
                                                                                                                                                                                                                      : 'border-gray-300 text-gray-600 hover:border-[#C85C6E] hover:text-[#C85C6E]' }}">
                        {{ $sub->name }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Sort + Count --}}
        <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-6">
            <p class="text-gray-500 text-sm font-light">
                Showing
                {{ $products instanceof \Illuminate\Pagination\LengthAwarePaginator ? $products->total() : count($products) }}
                results
            </p>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 text-sm border border-gray-200 rounded-full px-4 py-2 hover:border-gray-400 transition-colors">
                    Sort
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false"
                    class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-10"
                    style="display:none;">
                    @foreach (['newest' => 'Newest First', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'popular' => 'Most Popular'] as $val => $label)
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}"
                            class="block px-4 py-2 text-sm hover:bg-gray-50 {{ request('sort') === $val ? 'text-[#C85C6E] font-medium' : 'text-gray-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Products --}}
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
        <p class="text-gray-400 font-light text-lg">No products available, stay tuned!.</p>
        <a href="{{ route('gender.category.show', [
            'gender' => $gender->slug,
            'category' => $category->slug,
        ]) }}"
            class="inline-block mt-4 text-sm text-[#C85C6E] hover:underline">
            ← Back to {{ $category->name }}
        </a>
    </div>
    @endforelse

    @if (method_exists($products, 'links'))
        <div class="mt-10">{{ $products->links() }}</div>
    @endif
    </div>
</x-layout>
