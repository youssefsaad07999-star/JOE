<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-10">

        {{-- Breadcrumb --}}
        <x-breadcrumb :items="[
        ['label' => $gender->name, 'url' => route('gender.index', ['gender' => $gender->slug])],
        ['label' => $category->name, 'url' => route('gender.category.show', ['gender' => $gender->slug, 'category' => $category->slug])],
        ['label' => $subcategory->name, 'url' => route('gender.subcategory.show', ['gender' => $gender->slug, 'category' => $category->slug, 'subcategory' => $subcategory->slug])],
        ['label' => $product->name, 'url' => null],
    ]" />

        <div class="mt-8 grid md:grid-cols-2 gap-14 lg:gap-20">

            {{-- Images --}}
            <div x-data="{ activeImg: '{{ $product->image ?? '' }}' }">
                <div class="aspect-[4/5] rounded-3xl overflow-hidden bg-gray-100">
                    @if($product->image)
                        <img :src="activeImg ? '{{ asset('storage/') }}/' + activeImg : '{{ asset('storage/' . $product->image) }}'"
                            src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover"
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
                @if(isset($product->images) && $product->images->count() > 1)
                    <div class="flex gap-3 mt-4">
                        @foreach($product->images as $img)
                            <button @click="activeImg = '{{ $img->path }}'"
                                class="w-20 h-24 rounded-xl overflow-hidden border-2 transition-colors"
                                :class="activeImg === '{{ $img->path }}' ? 'border-[#C85C6E]' : 'border-transparent'">
                                <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="flex flex-col" x-data="{ selectedSize: '', selectedColor: '' }">
                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.25em] uppercase">
                    {{ $subcategory->name }}
                </p>
                <h1 class="font-['Cormorant_Garamond'] text-4xl md:text-5xl font-light mt-2 leading-tight">
                    {{ $product->name }}
                </h1>

                {{-- Price --}}
                <div class="flex items-center gap-3 mt-4">
                    <span class="text-3xl font-semibold">${{ number_format($product->price, 2) }}</span>
                    @if(isset($product->original_price) && $product->original_price > $product->price)
                        <span
                            class="text-gray-400 line-through text-lg">${{ number_format($product->original_price, 2) }}</span>
                        <span class="bg-rose-100 text-rose-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                            {{ round((1 - $product->price / $product->original_price) * 100) }}% OFF
                        </span>
                    @endif
                </div>

                {{-- Description --}}
                <p class="text-gray-600 font-light leading-relaxed mt-5 text-sm">
                    {{ $product->description ?? 'A carefully crafted piece designed to elevate your everyday style. Made with premium materials for lasting comfort and timeless appeal.' }}
                </p>

                <form action="" method="POST" class="mt-8 space-y-5">
                    {{-- {{ route('cart.store') }} --}}
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    {{-- Size --}}
                    @if(isset($product->sizes) && count($product->sizes) > 0)
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium">Size</label>
                                <a href="#" class="text-xs text-[#C85C6E] underline">Size Guide</a>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->sizes as $size)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="size" value="{{ $size }}" class="sr-only"
                                            x-on:change="selectedSize = '{{ $size }}'">
                                        <span
                                            class="flex items-center justify-center w-12 h-12 border rounded-xl text-sm font-medium
                                                                                                         transition-all duration-200 hover:border-[#C85C6E]"
                                            :class="selectedSize === '{{ $size }}' ? 'bg-[#1C1C1C] text-white border-[#1C1C1C]' : 'border-gray-200 text-gray-700'">
                                            {{ $size }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Color --}}
                    @if(isset($product->colors) && count($product->colors) > 0)
                        <div>
                            <label class="text-sm font-medium block mb-3">
                                Color: <span class="font-light text-gray-500" x-text="selectedColor || 'Select'"></span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->colors as $color)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="color" value="{{ $color['name'] }}" class="sr-only"
                                            x-on:change="selectedColor = '{{ $color['name'] }}'">
                                        <span class="w-9 h-9 rounded-full border-2 block transition-all"
                                            style="background-color: {{ $color['hex'] }}"
                                            :class="selectedColor === '{{ $color['name'] }}' ? 'border-[#1C1C1C] scale-110' : 'border-transparent hover:scale-110'"
                                            title="{{ $color['name'] }}">
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Quantity --}}
                    <div>
                        <label class="text-sm font-medium block mb-3">Quantity</label>
                        <div class="flex items-center border border-gray-200 rounded-full w-fit">
                            <button type="button" onclick="this.nextElementSibling.stepDown()"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-black transition text-lg">−</button>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock ?? 99 }}"
                                class="w-12 text-center border-none focus:outline-none text-sm font-medium">
                            <button type="button" onclick="this.previousElementSibling.stepUp()"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-black transition text-lg">+</button>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-[#1C1C1C] text-white py-4 rounded-full font-medium
                                       hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide">
                            Add to Cart
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

                {{-- Meta --}}
                <div class="mt-8 pt-6 border-t border-gray-100 space-y-2">
                    @if($product->stock ?? false)
                        <p class="text-sm text-gray-500 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            In stock · {{ $product->stock }} available
                        </p>
                    @endif
                    <p class="text-sm text-gray-500">✦ Free shipping on orders over $80</p>
                    <p class="text-sm text-gray-500">✦ Free returns within 30 days</p>
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="mt-20 pt-12 border-t border-gray-200">
                <h2 class="font-['Cormorant_Garamond'] text-3xl font-light mb-8">You May Also Like</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach($relatedProducts->take(4) as $related)
                        <x-product-card :product="$related" :gender="$currentGender->slug" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layout>