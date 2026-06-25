<x-admin.layout>
    <x-slot:breadcrumb>
        [['label' => 'Products', 'url' => route('admin.products.index')],
        ['label' => $product->name, 'url' => null]]
    </x-slot:breadcrumb>
    <x-slot:heading>Edit Product</x-slot:heading>
    <x-slot:subheading>{{ $product->name }}</x-slot:subheading>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PATCH')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Product Info --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold mb-5">Product Info</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Product Name</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]
                                          @error('name') border-red-400 @enderror">
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $product->slug) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono
                                          focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                            <textarea name="description" rows="4"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none
                                             focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Base Price
                                    <span class="text-gray-400 font-light text-xs ml-1">— used when no override</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" name="base_price"
                                        value="{{ old('base_price', $product->base_price) }}" step="0.01"
                                        min="0"
                                        class="w-full border border-gray-200 rounded-xl pl-7 pr-4 py-2.5 text-sm
                                                  focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subcategory</label>
                                <select name="category_id"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                               focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                                    @foreach ($genders as $gender)
                                        <optgroup label="{{ $gender->name }}">
                                            @foreach ($gender->children as $category)
                                        <optgroup label="  {{ $category->name }}">
                                            @foreach ($category->children as $sub)
                                                <option value="{{ $sub->id }}"
                                                    {{ old('category_id', $product->category_id) == $sub->id ? 'selected' : '' }}>
                                                    {{ $sub->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fit</label>
                                <select name="fit_id"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                               focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30">
                                    <option value="">Select fit</option>
                                    @foreach ($fits as $fit)
                                        <option value="{{ $fit->id }}"
                                            {{ old('fit_id', $product->fit_id) == $fit->id ? 'selected' : '' }}>
                                            {{ ucfirst($fit->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Brand</label>
                                <select name="brand_id"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                               focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30">
                                    <option value="">No brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── EXISTING VARIANTS ─────────────────────────────────── --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold">
                            Existing Variants
                            <span class="text-sm font-light text-gray-400 ml-2">
                                {{ $product->variants->count() }} total ·
                                {{ $product->variants->sum('stock_quantity') }} in stock
                            </span>
                        </h2>
                    </div>

                    <div class="border border-gray-100 rounded-xl overflow-x-auto">
                        <table class="w-full text-sm min-w-[650px]">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th
                                        class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                        Variant</th>
                                    <th
                                        class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                        SKU</th>
                                    <th
                                        class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                        Stock</th>
                                    <th
                                        class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                        Price override
                                        <span class="text-gray-400 font-normal normal-case">(blank = base)</span>
                                    </th>
                                    <th
                                        class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                        Active</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($product->variants as $variant)
                                    <tr
                                        class="{{ !$variant->is_active ? 'bg-gray-50 opacity-60' : 'hover:bg-gray-50' }} transition-colors">

                                        {{-- Variant identity --}}
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="w-3.5 h-3.5 rounded-full border border-gray-200 flex-shrink-0"
                                                    style="background-color: {{ $variant->color->hex_code ?? '#ccc' }}">
                                                </div>
                                                <span class="text-xs font-medium text-gray-700">
                                                    {{ ucfirst($variant->color->name) }}
                                                </span>
                                                <span
                                                    class="bg-gray-100 text-gray-600 text-[10px] px-2 py-0.5 rounded font-mono">
                                                    {{ $variant->size->name }}
                                                </span>
                                            </div>
                                            {{-- Pass the variant ID so the controller knows which row to update --}}
                                            <input type="hidden" name="variants[{{ $variant->id }}][id]"
                                                value="{{ $variant->id }}">
                                        </td>

                                        {{-- SKU --}}
                                        <td class="px-4 py-3">
                                            <input type="text" name="variants[{{ $variant->id }}][sku]"
                                                value="{{ old("variants.{$variant->id}.sku", $variant->sku) }}"
                                                class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs
                                                          focus:outline-none focus:ring-1 focus:ring-[#C85C6E] font-mono
                                                          min-w-[110px]">
                                        </td>

                                        {{-- Stock — independent per variant --}}
                                        <td class="px-4 py-3">
                                            <input type="number" name="variants[{{ $variant->id }}][stock_quantity]"
                                                value="{{ old("variants.{$variant->id}.stock_quantity", $variant->stock_quantity) }}"
                                                min="0"
                                                class="w-20 border rounded-lg px-2.5 py-1.5 text-xs
                                                          focus:outline-none focus:ring-1 focus:ring-[#C85C6E]
                                                          {{ $variant->stock_quantity === 0
                                                              ? 'border-red-300 bg-red-50'
                                                              : ($variant->stock_quantity <= 5
                                                                  ? 'border-amber-300 bg-amber-50'
                                                                  : 'border-gray-200') }}">
                                            @if ($variant->stock_quantity === 0)
                                                <p class="text-[10px] text-red-500 mt-0.5">Out of stock</p>
                                            @elseif($variant->stock_quantity <= 5)
                                                <p class="text-[10px] text-amber-500 mt-0.5">Low stock</p>
                                            @endif
                                        </td>

                                        {{-- Price override — independent per variant --}}
                                        <td class="px-4 py-3">
                                            <div class="relative w-28">
                                                <span
                                                    class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                                                <input type="number"
                                                    name="variants[{{ $variant->id }}][price_override]"
                                                    value="{{ old("variants.{$variant->id}.price_override", $variant->price_override) }}"
                                                    step="0.01" min="0" placeholder="Base price"
                                                    class="w-full border border-gray-200 rounded-lg pl-5 pr-2.5 py-1.5 text-xs
                                                              focus:outline-none focus:ring-1 focus:ring-[#C85C6E]">
                                            </div>
                                        </td>

                                        {{-- Active toggle --}}
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                    name="variants[{{ $variant->id }}][is_active]" value="1"
                                                    {{ $variant->is_active ? 'checked' : '' }} class="sr-only peer">
                                                <div
                                                    class="w-9 h-5 bg-gray-200 peer-checked:bg-[#C85C6E] rounded-full
                                                            after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                                            after:bg-white after:rounded-full after:h-4 after:w-4
                                                            after:transition-all peer-checked:after:translate-x-4">
                                                </div>
                                            </label>
                                        </td>

                                        {{-- Delete variant --}}
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <button type="button"
                                                onclick="if(confirm('Delete this variant? This cannot be undone.')) {
                                                        document.getElementById('delete-variant-{{ $variant->id }}').submit();
                                                    }"
                                                class="text-xs text-red-400 hover:text-red-600 transition-colors">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Hidden delete forms (one per variant, outside the main form) --}}
                    {{-- We close and reopen the form here --}}
                </div>

                {{-- ── ADD NEW VARIANTS ──────────────────────────────────── --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm" x-data="{
                    open: false,
                    selectedColors: [],
                    selectedSizes: [],
                    newVariants: [],
                
                    // Safely map values into an array using single quotes
                    existingCombinations: {{ json_encode($product->variants->map(fn($v) => $v->color_id . '_' . $v->size_id)->values()->toArray()) }},
                
                    generateVariants() {
                        this.newVariants = [];
                        this.selectedColors.forEach(colorId => {
                            this.selectedSizes.forEach(sizeId => {
                                const key = String(colorId) + '_' + String(sizeId);
                                if (!this.existingCombinations.includes(key)) {
                                    this.newVariants.push({
                                        color_id: colorId,
                                        size_id: sizeId,
                                        sku: '',
                                        stock_quantity: 0,
                                        price_override: '',
                                    });
                                }
                            });
                        });
                    },
                
                    // Clean single-quote lookups that won't break the HTML attribute
                    colorName(id) {
                        const el = document.querySelector('[data-new-color-id=\'' + id + '\']');
                        return el ? el.dataset.colorName : String(id);
                    },
                
                    colorHex(id) {
                        const el = document.querySelector('[data-new-color-id=\'' + id + '\']');
                        return el ? el.dataset.colorHex : '#ccc';
                    },
                
                    sizeName(id) {
                        const el = document.querySelector('[data-new-size-id=\'' + id + '\']');
                        return el ? el.dataset.sizeName : String(id);
                    }
                }">

                    <div class="flex items-center justify-between mb-2 gap-4">
                        <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold">Add New Variants</h2>
                        <button type="button" @click="open = !open"
                            class="text-xs text-[#C85C6E] hover:underline flex items-center gap-1 flex-shrink-0">
                            <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-45' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span x-text="open ? 'Collapse' : 'Add colour/size combinations'"></span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mb-4">
                        Only combinations that don't already exist will appear here.
                    </p>

                    <div x-show="open" x-transition style="display:none;">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-5">
                            {{-- Colors --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">1. Colours</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                    @foreach ($colors as $color)
                                        <label class="flex items-center gap-2.5 cursor-pointer select-none"
                                            data-new-color-id="{{ $color->id }}"
                                            data-color-name="{{ ucfirst($color->name) }}"
                                            data-color-hex="{{ $color->hex_code ?? '#ccc' }}">
                                            <input type="checkbox" :value="{{ $color->id }}"
                                                x-model="selectedColors" @change="generateVariants()"
                                                class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E]">
                                            <div class="w-4 h-4 rounded-full border border-gray-200 flex-shrink-0"
                                                style="background-color: {{ $color->hex_code ?? '#ccc' }}"></div>
                                            <span class="text-sm text-gray-700">{{ ucfirst($color->name) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Sizes --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">2. Sizes</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                    @foreach ($sizes as $size)
                                        <label class="flex items-center gap-2.5 cursor-pointer select-none"
                                            data-new-size-id="{{ $size->id }}"
                                            data-size-name="{{ $size->name }}">
                                            <input type="checkbox" :value="{{ $size->id }}"
                                                x-model="selectedSizes" @change="generateVariants()"
                                                class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E]">
                                            <span class="text-sm text-gray-700">{{ $size->name }}</span>
                                            <span class="text-xs text-gray-400">{{ ucfirst($size->type) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- No new combinations available --}}
                        <div x-show="selectedColors.length > 0 && selectedSizes.length > 0 && newVariants.length === 0"
                            class="mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700"
                            style="display:none;">
                            All selected colour/size combinations already exist on this product.
                        </div>

                        {{-- Info banner --}}
                        <div x-show="newVariants.length > 0"
                            class="mb-4 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-xs text-blue-700"
                            style="display:none;">
                            <strong x-text="newVariants.length"></strong> new variant(s) will be added.
                            Fill in stock and price for each below.
                        </div>

                        {{-- New variant matrix --}}
                        <div x-show="newVariants.length > 0" style="display:none;">
                            <div class="border border-gray-100 rounded-xl overflow-x-auto">
                                <table class="w-full text-sm min-w-[600px]">
                                    <thead class="bg-gray-50 border-b border-gray-100">
                                        <tr>
                                            <th
                                                class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Variant</th>
                                            <th
                                                class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                SKU</th>
                                            <th
                                                class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Stock</th>
                                            <th
                                                class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Price override
                                                <span class="text-gray-400 font-normal normal-case">(blank =
                                                    base)</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="(variant, i) in newVariants" :key="i">
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-3.5 h-3.5 rounded-full border border-gray-200 flex-shrink-0"
                                                            :style="'background-color:' + colorHex(variant.color_id)">
                                                        </div>
                                                        <span class="text-xs font-medium text-gray-700"
                                                            x-text="colorName(variant.color_id)"></span>
                                                        <span
                                                            class="bg-gray-100 text-gray-600 text-[10px] px-2 py-0.5 rounded font-mono"
                                                            x-text="sizeName(variant.size_id)"></span>
                                                    </div>
                                                    <input type="hidden" :name="`new_variants[${i}][color_id]`"
                                                        :value="variant.color_id">
                                                    <input type="hidden" :name="`new_variants[${i}][size_id]`"
                                                        :value="variant.size_id">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="text" :name="`new_variants[${i}][sku]`"
                                                        x-model="variant.sku" placeholder="SKU-001"
                                                        class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5
                                                                  text-xs focus:outline-none focus:ring-1
                                                                  focus:ring-[#C85C6E] font-mono min-w-[110px]">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" :name="`new_variants[${i}][stock_quantity]`"
                                                        x-model="variant.stock_quantity" min="0"
                                                        placeholder="0"
                                                        class="w-20 border border-gray-200 rounded-lg px-2.5 py-1.5
                                                                  text-xs focus:outline-none focus:ring-1 focus:ring-[#C85C6E]">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="relative w-32">
                                                        <span
                                                            class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                                                        <input type="number"
                                                            :name="`new_variants[${i}][price_override]`"
                                                            x-model="variant.price_override" step="0.01"
                                                            min="0" placeholder="Use base price"
                                                            class="w-full border border-gray-200 rounded-lg
                                                                      pl-5 pr-2.5 py-1.5 text-xs
                                                                      focus:outline-none focus:ring-1 focus:ring-[#C85C6E]">
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <p x-show="newVariants.length === 0 && selectedColors.length === 0"
                            class="text-sm text-gray-400 text-center py-4 border-2 border-dashed border-gray-100 rounded-xl">
                            Select colours and sizes above to see available combinations
                        </p>
                    </div>
                </div>

                {{-- IMAGES MANAGEMENT SYSTEM --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold mb-5">Images Gallery</h2>

                    {{-- SECTION 1: Product-level global images --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-3">
                            <h3 class="text-sm font-semibold text-gray-700">Product Images</h3>
                            <span class="text-xs text-gray-400">— shown before a colour is selected</span>
                        </div>

                        @php $productImages = $product->images->whereNull('color_id'); @endphp

                        @if ($productImages->count())
                            <div class="flex flex-wrap gap-3 mb-4">
                                @foreach ($productImages as $img)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $img->image_path) }}"
                                            class="w-20 h-24 object-cover rounded-xl border border-gray-200">
                                        @if ($img->is_primary)
                                            <div
                                                class="mt-1.5 w-full flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider text-rose-700 bg-rose-50/80 rounded-lg py-1 px-1.5 border border-rose-200 shadow-sm relative overflow-hidden">
                                                {{-- Left accent line decoration --}}
                                                <span class="absolute left-0 top-0 bottom-0 w-1 bg-rose-500"></span>

                                                {{-- Little check/star indicator icon --}}
                                                <svg class="w-2.5 h-2.5 text-rose-500 flex-shrink-0"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>

                                                <span>Primary</span>
                                            </div>
                                        @endif

                                        {{-- FIXED: Triggering the external form safely via JS onclick --}}
                                        <button type="button"
                                            onclick="if(confirm('Delete this image?')) document.getElementById('delete-image-{{ $img->id }}').submit();"
                                            class="absolute -top-2 -right-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-all duration-200 ease-in-out transform lg:translate-y-1 lg:group-hover:translate-y-0 w-7 h-7 bg-white text-gray-500 rounded-full flex items-center justify-center border border-gray-200 shadow-sm hover:border-red-200 hover:bg-red-50 hover:text-red-600 hover:scale-105 active:scale-95 cursor-pointer z-10">

                                            {{-- Minimalist Trash Can Icon SVG --}}
                                            <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center cursor-pointer hover:border-[#C85C6E] hover:bg-rose-50 transition-colors"
                            onclick="document.getElementById('product-images').click()" x-data="{ files: [] }">
                            <p class="text-sm text-gray-500">Add product images</p>
                            <input type="file" id="product-images" name="images[]" multiple accept="image/*"
                                class="hidden" @change="files = Array.from($event.target.files)">
                            <div class="mt-2 space-y-0.5" x-show="files.length > 0">
                                <template x-for="(f, i) in files" :key="i">
                                    <p class="text-xs text-gray-500" x-text="f.name"></p>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-100 mb-6"></div>

                    {{-- SECTION 2: Per-variant contextual image attachments grouped by Colour --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <h3 class="text-sm font-semibold text-gray-700">Variant Images (By Colour)</h3>
                            <span class="text-xs text-gray-400">— shown when a specific colour option is chosen</span>
                        </div>

                        <div class="space-y-4">
                            @foreach ($product->variants->groupBy('color_id') as $colorId => $colorVariants)
                                @php
                                    $firstVariant = $colorVariants->first();
                                    $colorName = $firstVariant->color->name ?? 'Unknown';
                                    $colorHex = $firstVariant->color->hex_code ?? '#ccc';
                                    $colorImages = $product->images->where('color_id', $colorId);
                                @endphp

                                <div class="border border-gray-100 rounded-xl overflow-hidden">
                                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border-b border-gray-100">
                                        <div class="w-3.5 h-3.5 rounded-full border border-gray-200 flex-shrink-0"
                                            style="background-color: {{ $colorHex }}"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($colorName) }}
                                            Gallery</span>
                                        <span class="text-xs text-gray-400 ml-auto">{{ $colorImages->count() }}
                                            image(s)</span>
                                    </div>

                                    <div class="p-4">
                                        @if ($colorImages->count())
                                            <div class="flex flex-wrap gap-2 mb-3">
                                                @foreach ($colorImages as $img)
                                                    <div class="relative group">
                                                        <img src="{{ asset('storage/' . $img->image_path) }}"
                                                            class="w-16 h-20 object-cover rounded-lg border border-gray-200">

                                                        {{-- FIXED: Triggering the external form safely via JS onclick --}}
                                                        <button type="button"
                                                            onclick="if(confirm('Delete this image?')) document.getElementById('delete-image-{{ $img->id }}').submit();"
                                                            class="absolute -top-2 -right-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-all duration-200 ease-in-out transform lg:translate-y-1 lg:group-hover:translate-y-0 w-7 h-7 bg-white text-gray-500 rounded-full flex items-center justify-center border border-gray-200 shadow-sm hover:border-red-200 hover:bg-red-50 hover:text-red-600 hover:scale-105 active:scale-95 cursor-pointer z-10">

                                                            {{-- Minimalist Trash Can Icon SVG --}}
                                                            <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                                                fill="none" stroke="currentColor" stroke-width="2"
                                                                viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div x-data="{ files: [] }">
                                            <label
                                                class="flex items-center gap-2 cursor-pointer border border-dashed border-gray-200 rounded-lg px-3 py-2 hover:border-[#C85C6E] hover:bg-rose-50 transition-colors">
                                                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span class="text-xs text-gray-500">Add images for all
                                                    {{ ucfirst($colorName) }} items</span>
                                                <input type="file" name="color_images[{{ $colorId }}][]"
                                                    multiple accept="image/*" class="hidden"
                                                    @change="files = Array.from($event.target.files)">
                                            </label>
                                            <div class="mt-2 space-y-0.5 pl-1" x-show="files.length > 0">
                                                <template x-for="(f, i) in files" :key="i">
                                                    <p class="text-xs text-gray-500" x-text="f.name"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-semibold text-sm mb-4">Status</h3>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E]">
                        <span class="text-sm text-gray-700">Active (visible in store)</span>
                    </label>
                    <div class="mt-5 space-y-2.5">
                        <button type="submit"
                            class="w-full bg-[#1C1C1C] text-white py-2.5 rounded-xl text-sm font-medium
                                       hover:bg-[#C85C6E] transition-colors">
                            Save Changes
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="block w-full text-center py-2.5 text-sm text-gray-500 hover:text-[#1C1C1C] transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>

                {{-- Stock overview --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm">
                    <h3 class="font-semibold text-sm mb-3">Stock Overview</h3>
                    <div class="space-y-2">
                        @foreach ($product->variants as $variant)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                                        style="background-color: {{ $variant->color->hex_code ?? '#ccc' }}"></div>
                                    <span class="text-gray-600">{{ ucfirst($variant->color->name) }}</span>
                                    <span class="font-mono text-gray-400">{{ $variant->size->name }}</span>
                                </div>
                                <span
                                    class="{{ $variant->stock_quantity === 0 ? 'text-red-600 font-semibold' : ($variant->stock_quantity <= 5 ? 'text-amber-600 font-medium' : 'text-gray-600') }}">
                                    {{ $variant->stock_quantity }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm text-xs text-gray-400 space-y-1.5">
                    <p>Created {{ $product->created_at->format('M d, Y') }}</p>
                    <p>Updated {{ $product->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </form>

    {{-- Variant Background Deletion Tasks Handlers --}}
    @foreach ($product->variants as $variant)
        <form id="delete-variant-{{ $variant->id }}"
            action="{{ route('admin.products.variants.destroy', $variant) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    {{-- NEW: Image Background Deletion Tasks Handlers (Sits completely outside the main form wrapper) --}}
    @foreach ($product->images as $img)
        <form id="delete-image-{{ $img->id }}" action="{{ route('admin.products.images.destroy', [$img]) }}"
            method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

</x-admin.layout>
