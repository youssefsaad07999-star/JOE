<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-10">

        <x-breadcrumb :items="[
            ['label' => $gender->name, 'url' => route('gender.index', $gender)],
            ['label' => $category->name, 'url' => route('gender.category.show', [$gender, $category])],
            [
                'label' => $subcategory->name,
                'url' => route('gender.subcategory.show', [$gender, $category, $subcategory]),
            ],
            ['label' => $product->name, 'url' => null],
        ]" />

        @php
            $variantData = $product->variants
                ->map(function ($v) use ($product) {
                    return [
                        'id' => $v->id,
                        'color_id' => $v->color_id,
                        'size_id' => $v->size_id,
                        'stock' => $v->stock_quantity,
                        'price' => $v->price_override
                            ? $v->price_override + $product->base_price
                            : $product->base_price,
                        'sku' => $v->sku,
                    ];
                })
                ->values();

            $colors = $product->variants->map(fn($v) => $v->color)->filter()->unique('id')->values();
            $sizes = $product->variants
                ->map(fn($v) => $v->size)
                ->filter()
                ->unique('id')
                ->sortBy('sort_order')
                ->values();

            /*
            |------------------------------------------------------------------
            | Build a color → images map.
            |
            | Structure:
            |   'global'      => ['path/a.jpg', 'path/b.jpg']  ← color_id IS NULL
            |   '<color_id>'  => ['path/c.jpg']                 ← color-specific
            |
            | When a colour is selected Alpine picks that key.
            | If no colour-specific images exist it falls back to 'global'.
            |------------------------------------------------------------------
            */
            $imageMap = [];

            foreach ($product->images as $img) {
                $key = $img->color_id ? (string) $img->color_id : 'global';
                $imageMap[$key][] = $img->image_path;
            }

            // Primary image — prefer global, fall back to whatever exists
            $defaultImages = $imageMap['global'] ?? (collect($imageMap)->first() ?? []);

            $primaryPath = $defaultImages[0] ?? null;

        @endphp

        <div class="mt-8 grid md:grid-cols-2 gap-14 lg:gap-20" x-data="{
            variants: {{ $variantData->toJson() }},
            selectedColor: null,
            selectedSize: null,
            qty: 1,
        
            {{-- Image map: 'global' or '<color_id>' → array of paths --}}
            imageMap: {{ json_encode($imageMap) }},
        
            {{-- The currently displayed image path --}}
            activeImg: '{{ $primaryPath }}',
        
            {{-- The thumbnail strip for the active colour --}}
            get activeThumbnails() {
                if (this.selectedColor) {
                    const key = String(this.selectedColor);
                    {{-- Use colour-specific images if they exist, otherwise fall back to global --}}
                    return this.imageMap[key] ??
                        this.imageMap['global'] ?? [];
                }
                return this.imageMap['global'] ?? [];
            },
        
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
                // TOGGLE FIX: If clicking the color that is already selected, clear it out
                if (this.selectedColor === colorId) {
                    this.selectedColor = null;
                    this.selectedSize = null;
                    this.qty = 1;
        
                    const globalImgs = this.imageMap['global'] ?? [];
                    if (globalImgs.length > 0) this.activeImg = globalImgs[0];
                    return;
                }
        
                this.selectedColor = colorId;
        
                {{-- Switch image to first image of this colour, or stay on global --}}
                const key = String(colorId);
                const imgs = this.imageMap[key] ?? this.imageMap['global'] ?? [];
                if (imgs.length > 0) this.activeImg = imgs[0];
        
                {{-- Reset size if it is no longer valid for the new colour --}}
                if (this.selectedSize && !this.availableSizes(this.selectedSize)) {
                    this.selectedSize = null;
                }
                this.qty = 1;
            }
        }">

            {{-- ── Images ─────────────────────────────────────────────────── --}}
            <div>

                {{-- Main image --}}
                <div class="aspect-[4/5] rounded-3xl overflow-hidden bg-gray-100">
                    @if ($primaryPath)
                        <img :src="activeImg
                            ?
                            '{{ asset('storage') }}/' + activeImg :
                            '{{ asset('storage/' . $primaryPath) }}'"
                            src="{{ asset('storage/' . $primaryPath) }}"
                            class="w-full h-full object-cover transition-opacity duration-300" alt="{{ $product->name }}">
                    @else
                        <div
                            class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200
                                    flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{--
                    Thumbnails — reactive via Alpine.
                    Shows thumbnails for the selected colour (or global if none).
                    Hidden when there is only one image.
                --}}
                <div class="flex gap-3 mt-4" x-show="activeThumbnails.length > 1">
                    <template x-for="(path, i) in activeThumbnails" :key="i">
                        <button @click="activeImg = path"
                            class="w-20 h-24 rounded-xl overflow-hidden border-2 transition-colors flex-shrink-0"
                            :class="activeImg === path ?
                                'border-[#C85C6E]' :
                                'border-transparent hover:border-gray-300'">
                            <img :src="'{{ asset('storage') }}/' + path" class="w-full h-full object-cover"
                                alt="{{ $product->name }}">
                        </button>
                    </template>
                </div>
            </div>

            {{-- ── Product info + variant picker ──────────────────────────── --}}
            <div class="flex flex-col">

                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.25em] uppercase">
                    {{ $subcategory->name }}
                    @if ($product->brand)
                        · {{ $product->brand->name }}
                    @endif
                </p>

                <h1 class="font-['Cormorant_Garamond'] text-4xl md:text-5xl font-light mt-2 leading-tight">
                    {{ $product->name }}
                </h1>

                @if ($product->fit)
                    <p class="text-gray-400 text-sm mt-1">{{ ucfirst($product->fit->name) }} fit</p>
                @endif

                <p class="text-3xl font-semibold mt-4" x-text="displayPrice"></p>

                <p class="text-gray-600 font-light leading-relaxed mt-5 text-sm">
                    {{ $product->description }}
                </p>

                <form action="{{ route('cart.store') }}" method="POST" class="mt-8 space-y-6">
                    @csrf
                    <input type="hidden" name="product_variant_id" :value="selectedVariant?.id">
                    <input type="hidden" name="quantity" :value="qty">

                    {{-- Colour picker --}}
                    @if ($colors->isNotEmpty())
                        <div>
                            <label class="text-sm font-medium flex items-center justify-between mb-3">
                                <div>
                                    Colour:
                                    <span class="font-light text-gray-500">
                                        @foreach ($colors as $color)
                                            <span x-show="selectedColor === {{ $color->id }}">
                                                {{ ucfirst($color->name) }}
                                            </span>
                                        @endforeach
                                        <span x-show="!selectedColor">Select</span>
                                    </span>
                                </div>

                                <button type="button" x-show="selectedColor" x-cloak
                                    @click="selectColor(selectedColor)"
                                    class="text-xs text-gray-400 hover:text-black underline transition-colors cursor-pointer">
                                    Clear selection
                                </button>
                            </label>

                            <div class="flex flex-wrap gap-2">
                                @foreach ($colors as $color)
                                    <button type="button" @click="selectColor({{ $color->id }})"
                                        class="w-9 h-9 rounded-full border-2 transition-all duration-150
                                                   hover:scale-110 focus:outline-none cursor-pointer"
                                        :class="selectedColor === {{ $color->id }} ?
                                            'border-[#1C1C1C] scale-110 ring-2 ring-offset-1 ring-[#C85C6E]' :
                                            'border-transparent'"
                                        style="background-color: {{ $color->hex_code ?? '#ccc' }}"
                                        title="{{ ucfirst($color->name) }}" aria-label="{{ ucfirst($color->name) }}">
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Hint: colour has its own images --}}
                        <p class="text-xs text-gray-400 mt-2"
                            x-show="selectedColor && imageMap[String(selectedColor)] && imageMap[String(selectedColor)].length > 0">
                            Showing photos for this colour
                        </p>
                    @endif

                    {{-- Size picker --}}
                    @if ($sizes->isNotEmpty())
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium">Size</label>
                                <a href="#" class="text-xs text-[#C85C6E] underline">Size Guide</a>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($sizes as $size)
                                    <button type="button"
                                        @click="if (availableSizes({{ $size->id }}) && stockForSize({{ $size->id }}) > 0) selectedSize = {{ $size->id }}"
                                        class="min-w-[3rem] h-12 px-3 border rounded-xl text-sm font-medium
                                                   transition-all duration-150 focus:outline-none"
                                        :class="{
                                            'bg-[#1C1C1C] text-white border-[#1C1C1C]': selectedSize ===
                                                {{ $size->id }},
                                            'border-gray-200 text-gray-700 hover:border-[#C85C6E]': selectedSize !==
                                                {{ $size->id }} &&
                                                availableSizes({{ $size->id }}) &&
                                                stockForSize({{ $size->id }}) > 0,
                                            'border-gray-100 text-gray-300 cursor-not-allowed line-through':
                                                !availableSizes({{ $size->id }}) ||
                                                stockForSize({{ $size->id }}) === 0
                                        }"
                                        :disabled="!availableSizes({{ $size->id }}) || stockForSize({{ $size->id }}) ===
                                            0">
                                        {{ $size->name }}
                                    </button>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-400 mt-2" x-show="!selectedColor">
                                Select a colour first to see available sizes
                            </p>
                        </div>
                    @endif

                    {{-- Quantity --}}
                    <div x-show="selectedVariant" style="display:none;">
                        <label class="text-sm font-medium block mb-3">Quantity</label>
                        <div class="flex items-center border border-gray-200 rounded-full w-fit">
                            <button type="button" @click="qty = Math.max(1, qty - 1)"
                                class="w-10 h-10 flex items-center justify-center text-gray-500
                                           hover:text-black transition text-lg">−</button>
                            <span class="w-10 text-center text-sm font-medium" x-text="qty"></span>
                            <button type="button" @click="qty = Math.min(maxQty, qty + 1)"
                                class="w-10 h-10 flex items-center justify-center text-gray-500
                                           hover:text-black transition text-lg">+</button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">
                            <span x-text="maxQty"></span> available
                        </p>
                    </div>

                    {{-- Add to bag --}}
                    <div class="flex gap-3 pt-1">
                        <button type="submit" :disabled="!selectedVariant || !inStock"
                            class="flex-1 py-4 rounded-full font-medium text-sm tracking-wide
                                       transition-colors duration-300"
                            :class="selectedVariant && inStock ?
                                'bg-[#1C1C1C] text-white hover:bg-[#C85C6E] cursor-pointer' :
                                'bg-gray-200 text-gray-400 cursor-not-allowed'">
                            <span x-show="!selectedColor">Choose a Colour</span>
                            <span x-show="selectedColor && !selectedSize" style="display:none;">Choose a Size</span>
                            <span x-show="selectedVariant && inStock" style="display:none;">Add to Bag</span>
                            <span x-show="selectedVariant && !inStock" style="display:none;">Out of Stock</span>
                        </button>

                        <button type="button"
                            class="w-14 h-14 border border-gray-200 rounded-full flex items-center justify-center
                                       hover:border-rose-400 hover:text-rose-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 space-y-2 text-sm text-gray-500">
                    <p x-show="selectedVariant">
                        SKU: <span class="font-mono text-xs" x-text="selectedVariant?.sku"></span>
                    </p>
                    <p>✦ Free shipping on orders over $80</p>
                    <p>✦ Free returns within 30 days</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
