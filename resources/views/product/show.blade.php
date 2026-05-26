<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-10">

        <x-breadcrumb :items="[
        ['label' => $gender->name, 'url' => route('gender.index', $gender)],
        ['label' => $category->name, 'url' => route('gender.category.show', [$gender, $category])],
        ['label' => $subcategory->name, 'url' => route('gender.subcategory.show', [$gender, $category, $subcategory])],
        ['label' => $product->name, 'url' => null],
    ]" />

        {{--
        Prepare variant data for Alpine.
        We do this in PHP so Alpine just reads plain arrays — no complex JS needed.
        --}}
        @php
            // All variants with their resolved relationships (eager loaded by Route::bind)
            $variantData = $product->variants->map(fn($v) => [
                'id' => $v->id,
                'color_id' => $v->color_id,
                'size_id' => $v->size_id,
                'stock' => $v->stock_quantity,
                'price' => $v->price_override ? $v->price_override + $product->base_price : $product->base_price,
                'sku' => $v->sku,
            ])->values();

            // Unique colors across all variants
            $colors = $product->variants
                ->map(fn($v) => $v->color)
                ->filter()
                ->unique('id')
                ->values();

            // Unique sizes across all variants, sorted: alpha sizes first then numeric
            $sizes = $product->variants
                ->map(fn($v) => $v->size)
                ->filter()
                ->unique('id')
                ->sortBy('sort_order')
                ->values();

            // Primary image
            $primaryImage = $product->images->first();
        @endphp

        <div class="mt-8 grid md:grid-cols-2 gap-14 lg:gap-20" x-data="{
            variants: {{ $variantData->toJson() }},
            selectedColor: null,
            selectedSize: null,
            qty: 1,

            {{-- Sizes available for the currently selected color --}}
            availableSizes(sizeId) {
                if (!this.selectedColor) return false;
                return this.variants.some(
                    v => v.color_id == this.selectedColor && v.size_id == sizeId
                );
            },

            stockForSize(sizeId) {
                const v = this.variants.find(
                    v => v.color_id == this.selectedColor && v.size_id == sizeId
                );
                return v ? v.stock : 0;
            },

            {{-- The specific variant matching both selections --}}
            get selectedVariant() {
                if (!this.selectedColor || !this.selectedSize) return null;
                return this.variants.find(
                    v => v.color_id == this.selectedColor && v.size_id == this.selectedSize
                ) || null;
            },

            get inStock() {
                return this.selectedVariant && this.selectedVariant.stock > 0;
            },

            get maxQty() {
                return this.selectedVariant ? this.selectedVariant.stock : 1;
            },

            get displayPrice() {
                const base = {{ $product->base_price }};
                return '$' + parseFloat(
                    this.selectedVariant ? this.selectedVariant.price : base
                ).toFixed(2);
            },

            selectColor(colorId) {
                this.selectedColor = colorId;
                {{-- Reset size if it is no longer available for the new color --}}
                if (this.selectedSize && !this.availableSizes(this.selectedSize)) {
                    this.selectedSize = null;
                }
                this.qty = 1;
            }
        }">

            {{-- ── Images ──────────────────────────────────────────────────── --}}
            <div x-data="{ activeImg: '{{ $primaryImage?->path ?? '' }}' }">

                <div class="aspect-[4/5] rounded-3xl overflow-hidden bg-gray-100">
                    @if($primaryImage)
                        <img :src="activeImg
                                                                    ? '{{ asset('storage') }}/' + activeImg
                                                                    : '{{ asset('storage/' . $primaryImage->path) }}'"
                            src="{{ asset('storage/' . $primaryImage->path) }}" class="w-full h-full object-cover"
                            alt="{{ $product->name }}">
                    @else
                        <div
                            class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Thumbnails --}}
                @if($product->images->count() > 1)
                    <div class="flex gap-3 mt-4">
                        @foreach($product->images as $img)
                            <button @click="activeImg = '{{ $img->path }}'"
                                class="w-20 h-24 rounded-xl overflow-hidden border-2 transition-colors flex-shrink-0"
                                :class="activeImg === '{{ $img->path }}'
                                                                                                                ? 'border-[#C85C6E]'
                                                                                                                : 'border-transparent hover:border-gray-300'">
                                <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover"
                                    alt="{{ $product->name }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── Product info + variant picker ──────────────────────────── --}}
            <div class="flex flex-col">

                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.25em] uppercase">
                    {{ $subcategory->name }}
                    @if($product->brand) · {{ $product->brand->name }} @endif
                </p>

                <h1 class="font-['Cormorant_Garamond'] text-4xl md:text-5xl font-light mt-2 leading-tight">
                    {{ $product->name }}
                </h1>

                @if($product->fit)
                    <p class="text-gray-400 text-sm mt-1">{{ ucfirst($product->fit->name) }} fit</p>
                @endif

                {{-- Price — updates reactively when a variant is selected --}}
                <p class="text-3xl font-semibold mt-4" x-text="displayPrice"></p>

                <p class="text-gray-600 font-light leading-relaxed mt-5 text-sm">
                    {{ $product->description }}
                </p>


                <form action="{{ route('cart.store') }}" method="POST" class="mt-8 space-y-6">
                    @csrf
                    {{-- Variant ID is filled by Alpine when both color + size are chosen --}}
                    <input type="hidden" name="product_variant_id" :value="selectedVariant?.id">
                    <input type="hidden" name="quantity" :value="qty">

                    {{-- ── Color ─────────────────────────────────────────── --}}
                    @if($colors->isNotEmpty())
                        <div>
                            <label class="text-sm font-medium block mb-3">
                                Colour:
                                <span class="font-light text-gray-500">
                                    @foreach($colors as $color)
                                        <span x-show="selectedColor === {{ $color->id }}">{{ ucfirst($color->name) }}</span>
                                    @endforeach
                                    <span x-show="!selectedColor">Select</span>
                                </span>
                            </label>

                            <div class="flex flex-wrap gap-2">
                                @foreach($colors as $color)
                                    <button type="button" @click="selectColor({{ $color->id }})"
                                        class="w-9 h-9 rounded-full border-2 transition-all duration-150 hover:scale-110 focus:outline-none"
                                        :class="selectedColor === {{ $color->id }}
                                                                                                                ? 'border-[#1C1C1C] scale-110 ring-2 ring-offset-1 ring-[#C85C6E]'
                                                                                                                : 'border-transparent'"
                                        style="background-color: {{ $color->hex_code ?? '#ccc' }}"
                                        title="{{ ucfirst($color->name) }}" aria-label="{{ ucfirst($color->name) }}">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── Size ──────────────────────────────────────────── --}}
                    @if($sizes->isNotEmpty())
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium">Size</label>
                                <a href="#" class="text-xs text-[#C85C6E] underline">Size Guide</a>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @foreach($sizes as $size)
                                    <button type="button"
                                        @click="if (availableSizes({{ $size->id }}) && stockForSize({{ $size->id }}) > 0) selectedSize = {{ $size->id }}"
                                        class="min-w-[3rem] h-12 px-3 border rounded-xl text-sm font-medium transition-all duration-150 focus:outline-none"
                                        :class="{
                                                                                                                'bg-[#1C1C1C] text-white border-[#1C1C1C]':
                                                                                                                    selectedSize === {{ $size->id }},
                                                                                                                'border-gray-200 text-gray-700 hover:border-[#C85C6E]':
                                                                                                                    selectedSize !== {{ $size->id }}
                                                                                                                    && availableSizes({{ $size->id }})
                                                                                                                    && stockForSize({{ $size->id }}) > 0,
                                                                                                                'border-gray-100 text-gray-300 cursor-not-allowed line-through':
                                                                                                                    !availableSizes({{ $size->id }})
                                                                                                                    || stockForSize({{ $size->id }}) === 0
                                                                                                            }"
                                        :disabled="!availableSizes({{ $size->id }}) || stockForSize({{ $size->id }}) === 0">
                                        {{ $size->name }}
                                    </button>
                                @endforeach
                            </div>

                            <p class="text-xs text-gray-400 mt-2" x-show="!selectedColor">
                                Select a colour first to see available sizes
                            </p>
                        </div>
                    @endif

                    {{-- ── Quantity ───────────────────────────────────────── --}}
                    <div x-show="selectedVariant" style="display:none;">
                        <label class="text-sm font-medium block mb-3">Quantity</label>
                        <div class="flex items-center border border-gray-200 rounded-full w-fit">
                            <button type="button" @click="qty = Math.max(1, qty - 1)"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-black transition text-lg">
                                −
                            </button>
                            <span class="w-10 text-center text-sm font-medium" x-text="qty"></span>
                            <button type="button" @click="qty = Math.min(maxQty, qty + 1)"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-black transition text-lg">
                                +
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">
                            <span x-text="maxQty"></span> available
                        </p>
                    </div>

                    {{-- ── Add to Bag ─────────────────────────────────────── --}}
                    <div class="flex gap-3 pt-1">
                        <button type="submit" :disabled="!selectedVariant || !inStock"
                            class="flex-1 py-4 rounded-full font-medium text-sm tracking-wide transition-colors duration-300"
                            :class="selectedVariant && inStock
                                ? 'bg-[#1C1C1C] text-white hover:bg-[#C85C6E] cursor-pointer'
                                : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                            <span x-show="!selectedColor">Choose a Colour</span>
                            <span x-show="selectedColor && !selectedSize" style="display:none;">Choose a Size</span>
                            <span x-show="selectedVariant && inStock" style="display:none;">Add to Bag</span>
                            <span x-show="selectedVariant && !inStock" style="display:none;">Out of Stock</span>
                        </button>

                        <button type="button" class="w-14 h-14 border border-gray-200 rounded-full flex items-center justify-center
                                   hover:border-rose-400 hover:text-rose-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                </form>

                {{-- ── Meta ───────────────────────────────────────────────── --}}
                <div class="mt-8 pt-6 border-t border-gray-100 space-y-2 text-sm text-gray-500">
                    <p x-show="selectedVariant">
                        SKU: <span class="font-mono text-xs" x-text="selectedVariant?.sku"></span>
                    </p>
                    <p>✦ Free shipping on orders over $80</p>
                    <p>✦ Free returns within 30 days</p>
                </div>
            </div>
        </div>

        {{-- ── Related products ───────────────────────────────────────────── --}}
        {{-- @if($relatedProducts->isNotEmpty())
        <div class="mt-20 pt-12 border-t border-gray-200">
            <h2 class="font-['Cormorant_Garamond'] text-3xl font-light mb-8">You May Also Like</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                @foreach($relatedProducts->take(4) as $related)
                <x-product-card :product="$related" :gender="$gender" :category="$category"
                    :subcategory="$subcategory" />
                @endforeach
            </div>
        </div>
        @endif --}}

    </div>
</x-layout>