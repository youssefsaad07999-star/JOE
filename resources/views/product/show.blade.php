<x-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 md:py-12">

        {{-- Breadcrumb --}}
        <div class="opacity-90 transition-opacity hover:opacity-100 text-xs md:text-sm">
            <x-breadcrumb :items="[
                ['label' => $gender->name, 'url' => route('gender.index', $gender)],
                ['label' => $category->name, 'url' => route('gender.category.show', [$gender, $category])],
                [
                    'label' => $subcategory->name,
                    'url' => route('gender.subcategory.show', [$gender, $category, $subcategory]),
                ],
                ['label' => $product->name, 'url' => null],
            ]" />
        </div>

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

            $imageMap = [];

            foreach ($product->images as $img) {
                $key = $img->color_id ? (string) $img->color_id : 'global';
                $imageMap[$key][] = $img->image_path;
            }

            $defaultImages = $imageMap['global'] ?? (collect($imageMap)->first() ?? []);
            $primaryPath = $defaultImages[0] ?? null;
        @endphp

        <div class="mt-6 md:mt-10 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start" x-data="{
            variants: {{ $variantData->toJson() }},
            selectedColor: null,
            selectedSize: null,
            qty: 1,
        
            imageMap: {{ json_encode($imageMap) }},
            activeImg: '{{ $primaryPath }}',
        
            get activeThumbnails() {
                if (this.selectedColor) {
                    const key = String(this.selectedColor);
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
                if (this.selectedColor === colorId) {
                    this.selectedColor = null;
                    this.selectedSize = null;
                    this.qty = 1;
        
                    const globalImgs = this.imageMap['global'] ?? [];
                    if (globalImgs.length > 0) this.activeImg = globalImgs[0];
                    return;
                }
        
                this.selectedColor = colorId;
        
                const key = String(colorId);
                const imgs = this.imageMap[key] ?? this.imageMap['global'] ?? [];
                if (imgs.length > 0) this.activeImg = imgs[0];
        
                if (this.selectedSize && !this.availableSizes(this.selectedSize)) {
                    this.selectedSize = null;
                }
                this.qty = 1;
            }
        }">

            {{-- ── Gallery Space (7/12 Columns on Wide Screens) ──────────────── --}}
            <div class="lg:col-span-7 w-full" x-data="{ lightboxOpen: false }">

                <div class="flex flex-col-reverse md:flex-row gap-4 items-start">

                    {{-- ── Vertical thumbnail strip (desktop left, mobile bottom) ── --}}
                    <div class="flex md:flex-col gap-2.5 overflow-x-auto md:overflow-y-auto max-h-[100px] md:max-h-[560px] w-full md:w-auto
        scrollbar-none pb-1 md:pb-0 md:pr-1 shrink-0"
                        x-show="activeThumbnails.length > 1">
                        <template x-for="(path, i) in activeThumbnails" :key="i">
                            <button @click="activeImg = path"
                                class="w-14 h-18 md:w-16 md:h-20 rounded-xl overflow-hidden border transition-all duration-300 shrink-0 snap-start shadow-sm"
                                :class="activeImg === path ?
                                    'border-[#C85C6E] ring-2 ring-[#C85C6E]/10 scale-95 opacity-100' :
                                    'border-gray-200/60 bg-white/50 opacity-70 hover:opacity-100 hover:border-gray-400 hover:scale-105'">
                                {{-- Changed to object-cover to make thumbnails look completely filled and clean --}}
                                <img :src="'{{ asset('storage') }}/' + path"
                                    class="w-full h-full object-cover pointer-events-none" alt="{{ $product->name }}">
                            </button>
                        </template>
                    </div>

                    {{-- ── Main image container ── --}}
                    <div class="relative flex-1 w-full group cursor-zoom-in flex items-center justify-center h-[420px] sm:h-[480px] md:h-[560px]"
                        @click="lightboxOpen = true">

                        @if ($primaryPath)
                            {{-- Border, shadow, and rounding are now on the image itself, dynamically fitting its exact aspect ratio --}}
                            <img :src="activeImg
                                ?
                                '{{ asset('storage') }}/' + activeImg :
                                '{{ asset('storage/' . $primaryPath) }}'"
                                src="{{ asset('storage/' . $primaryPath) }}"
                                class="max-w-full max-h-full w-auto h-auto object-contain rounded-2xl border border-gray-200/50 shadow-[0_8px_30px_rgb(0,0,0,0.03)] group-hover:shadow-[0_16px_40px_rgb(0,0,0,0.06)] transition-all duration-500 ease-out group-hover:scale-[1.01]"
                                alt="{{ $product->name }}">
                        @else
                            <div
                                class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center rounded-2xl border border-gray-200/40">
                                <svg class="w-16 h-16 text-gray-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif

                        {{-- Zoom hint badge ── --}}
                        @if ($primaryPath)
                            <div
                                class="absolute bottom-4 right-4 bg-gray-900/90 backdrop-blur-md text-white
                text-xs px-3 py-2 rounded-full flex items-center gap-1.5 shadow-md
                opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-1 group-hover:translate-y-0 pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                                Click to zoom
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── Lightbox ── --}}
                @if ($primaryPath)
                    <div x-show="lightboxOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" @click.self="lightboxOpen = false"
                        @keydown.escape.window="lightboxOpen = false"
                        class="fixed inset-0 bg-black/90 backdrop-blur-md z-50 flex items-center justify-center p-4 md:p-8"
                        style="display:none;">

                        {{-- Close button --}}
                        <button @click="lightboxOpen = false"
                            class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        {{-- Prev / Next arrows --}}
                        <button
                            @click.stop="const imgs = activeThumbnails; const i = imgs.indexOf(activeImg); if (i > 0) activeImg = imgs[i - 1]"
                            x-show="activeThumbnails.length > 1"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button
                            @click.stop="const imgs = activeThumbnails; const i = imgs.indexOf(activeImg); if (i < imgs.length - 1) activeImg = imgs[i + 1]"
                            x-show="activeThumbnails.length > 1"
                            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        {{-- Full-size image --}}
                        <div class="max-w-2xl max-h-full w-full flex items-center justify-center"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="scale-95 opacity-0"
                            x-transition:enter-end="scale-100 opacity-100">
                            <img :src="activeImg ? '{{ asset('storage') }}/' + activeImg :
                                '{{ asset('storage/' . $primaryPath) }}'"
                                src="{{ asset('storage/' . $primaryPath) }}"
                                class="max-h-[85vh] max-w-full w-auto rounded-2xl object-contain shadow-2xl"
                                alt="{{ $product->name }}">
                        </div>

                        {{-- Dot indicators --}}
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2"
                            x-show="activeThumbnails.length > 1">
                            <template x-for="(path, i) in activeThumbnails" :key="i">
                                <button @click.stop="activeImg = path"
                                    class="w-1.5 h-1.5 rounded-full transition-all duration-200"
                                    :class="activeImg === path ? 'bg-white w-4' : 'bg-white/40 hover:bg-white/70'">
                                </button>
                            </template>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ── Content Buy-Box Information (5/12 Columns) ────────────────── --}}
            <div class="lg:col-span-5 w-full flex flex-col lg:sticky lg:top-8">

                {{-- Category Hierarchy Tags --}}
                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.2em] uppercase">
                    {{ $subcategory->name }}
                    @if ($product->brand)
                        <span class="text-gray-300 mx-1.5">·</span>{{ $product->brand->name }}
                    @endif
                </p>

                {{-- Document Title --}}
                <h1
                    class="font-['Cormorant_Garamond'] text-3xl md:text-4xl lg:text-5xl font-light text-gray-900 mt-2 tracking-tight leading-tight">
                    {{ $product->name }}
                </h1>

                @if ($product->fit)
                    <p class="text-gray-400 text-xs md:text-sm mt-1 font-medium tracking-wide uppercase">
                        {{ $product->fit->name }} fit</p>
                @endif

                {{-- Price Callout --}}
                <p class="text-2xl md:text-3xl font-semibold text-gray-900 mt-4 tracking-tight" x-text="displayPrice">
                </p>

                {{-- Core Body Description --}}
                <div class="mt-5 border-t border-gray-100 pt-5">
                    <p class="text-gray-600 font-light leading-relaxed text-sm">
                        {!! $product->description !!}
                    </p>
                </div>

                {{-- Purchase Request Pipeline --}}
                <form @submit.prevent="$dispatch('add-to-cart', { variantId: selectedVariant?.id, quantity: qty })"
                    class="mt-6 space-y-6">


                    {{-- Attribute 1: Color Configuration Swatches --}}
                    @if ($colors->isNotEmpty())
                        <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                            <label
                                class="text-xs font-semibold uppercase tracking-wider text-gray-700 flex items-center justify-between mb-3 select-none">
                                <div>
                                    Colour:
                                    <span class="font-normal normal-case text-gray-500 ml-1">
                                        @foreach ($colors as $color)
                                            <span x-show="selectedColor === {{ $color->id }}" x-transition>
                                                {{ ucfirst($color->name) }}
                                            </span>
                                        @endforeach
                                        <span x-show="!selectedColor" class="text-gray-400 italic">Select
                                            option</span>
                                    </span>
                                </div>

                                <button type="button" x-show="selectedColor" x-cloak
                                    @click="selectColor(selectedColor)"
                                    class="text-xs font-normal text-gray-400 hover:text-black transition-colors cursor-pointer capitalize">
                                    Reset
                                </button>
                            </label>

                            <div class="flex flex-wrap gap-2.5">
                                @foreach ($colors as $color)
                                    <button type="button" @click="selectColor({{ $color->id }})"
                                        class="w-8 h-8 rounded-full border-2 transition-all duration-200 hover:scale-110 focus:outline-none cursor-pointer relative shadow-sm"
                                        :class="selectedColor === {{ $color->id }} ?
                                            'border-black ring-2 ring-offset-2 ring-[#C85C6E]' :
                                            'border-white ring-1 ring-gray-200'"
                                        style="background-color: {{ $color->hex_code ?? '#ccc' }}"
                                        title="{{ ucfirst($color->name) }}"
                                        aria-label="{{ ucfirst($color->name) }}">
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Specific Contextual Photo Feedback Note --}}
                        <p class="text-xs text-gray-400 mt-2 font-light italic"
                            x-show="selectedColor && imageMap[String(selectedColor)] && imageMap[String(selectedColor)].length > 0"
                            x-transition>
                            ✦ Showing photos tailored to this selection
                        </p>
                    @endif

                    {{-- Attribute 2: Size Configuration Triggers --}}
                    @if ($sizes->isNotEmpty())
                        <div class="space-y-3">
                            <div
                                class="flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-gray-700 select-none">
                                <label>Size</label>
                                <a href="#"
                                    class="text-[#C85C6E] hover:text-black transition-colors normal-case font-normal">Size
                                    Guide</a>
                            </div>

                            <div
                                class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                                @foreach ($sizes as $size)
                                    <button type="button"
                                        @click="if (availableSizes({{ $size->id }}) && stockForSize({{ $size->id }}) > 0) selectedSize = {{ $size->id }}"
                                        class="h-11 px-2 border rounded-xl text-xs font-medium transition-all duration-200 focus:outline-none flex items-center justify-center cursor-pointer uppercase select-none shadow-sm"
                                        :class="{
                                            'bg-gray-900 text-white border-gray-900 shadow-md': selectedSize ===
                                                {{ $size->id }},
                                            'border-gray-200 text-gray-700 bg-white hover:border-[#C85C6E] hover:text-[#C85C6E]': selectedSize !==
                                                {{ $size->id }} && availableSizes({{ $size->id }}) &&
                                                stockForSize({{ $size->id }}) > 0,
                                            'border-gray-100 text-gray-300 bg-gray-50/50 cursor-not-allowed line-through shadow-none':
                                                !availableSizes({{ $size->id }}) || stockForSize(
                                                    {{ $size->id }}) === 0
                                        }"
                                        :disabled="!availableSizes({{ $size->id }}) || stockForSize({{ $size->id }}) ===
                                            0">
                                        {{ $size->name }}
                                    </button>
                                @endforeach
                            </div>

                            <p class="text-xs text-gray-400 font-light italic" x-show="!selectedColor">
                                Please configure color configuration first to review size availability.
                            </p>
                        </div>
                    @endif

                    {{-- Attribute 3: Incremental Item Counter Component --}}
                    <div x-show="selectedVariant" x-transition
                        class="bg-gray-50/50 border border-gray-100 p-4 rounded-2xl" style="display:none;">
                        <label
                            class="text-xs font-semibold uppercase tracking-wider text-gray-700 block mb-2 select-none">Quantity</label>
                        <div class="flex items-center justify-between sm:justify-start gap-4">
                            <div class="flex items-center border border-gray-200 bg-white rounded-full p-1 shadow-sm">
                                <button type="button" @click="qty = Math.max(1, qty - 1)"
                                    class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-black transition-colors rounded-full text-lg font-light select-none cursor-pointer">−</button>
                                <span class="w-10 text-center text-sm font-semibold text-gray-800"
                                    x-text="qty"></span>
                                <button type="button" @click="qty = Math.min(maxQty, qty + 1)"
                                    class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-black transition-colors rounded-full text-lg font-light select-none cursor-pointer">+</button>
                            </div>

                            <div class="shrink-0">
                                @error('quantity')
                                    <p class="text-xs text-red-600 font-medium flex items-center gap-1.5">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                        {{ $message }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 font-medium">
                                        <span class="text-gray-700 font-semibold" x-text="maxQty"></span> units currently
                                        available
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Interactive Form Actions Execution Bar --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        {{-- 🎯 FIXED: Changed "flex-1" to "w-full sm:flex-1" --}}
                        <button type="submit" :disabled="!selectedVariant || !inStock"
                            class="w-full sm:flex-1 h-14 rounded-full font-medium text-sm tracking-wide transition-all duration-300 shadow-sm border select-none uppercase"
                            :class="selectedVariant && inStock ?
                                'bg-gray-900 border-gray-900 text-white hover:bg-[#C85C6E] hover:border-[#C85C6E] hover:shadow-md cursor-pointer' :
                                'bg-gray-100 border-gray-100 text-gray-400 cursor-not-allowed'">
                            <span x-show="!selectedColor">Choose a Colour</span>
                            <span x-show="selectedColor && !selectedSize" style="display:none;">Choose a Size</span>
                            <span x-show="selectedVariant && inStock" style="display:none;">Add to Bag</span>
                            <span x-show="selectedVariant && !inStock" style="display:none;">Out of Stock</span>
                        </button>

                        <button type="button" aria-label="Add to Wishlist"
                            class="h-14 w-full sm:w-14 border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:border-rose-400 hover:text-rose-500 transition-all cursor-pointer bg-white shadow-sm shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                </form>

                {{-- Logistics Metrics Box --}}
                <footer class="mt-8 pt-6 border-t border-gray-100 space-y-3 text-xs md:text-sm text-gray-500">
                    <div x-show="selectedVariant" x-transition style="display: none;"
                        class="font-medium text-gray-600">
                        SKU: <span class="font-mono text-xs text-gray-400 ml-1" x-text="selectedVariant?.sku"></span>
                    </div>
                    <p class="flex items-center gap-2">
                        <span class="text-[#C85C6E] text-xs">✦</span> Free shipping on standard orders over $80
                    </p>
                    <p class="flex items-center gap-2">
                        <span class="text-[#C85C6E] text-xs">✦</span> Simplified returns process managed within 30 days
                    </p>
                </footer>
            </div>
        </div>

    </div>
</x-layout>
