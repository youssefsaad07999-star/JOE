<x-admin.layout>
    <x-slot:breadcrumb>
        [['label' => 'Products', 'url' => route('admin.products.index')],
        ['label' => 'New Product', 'url' => null]]
    </x-slot:breadcrumb>
    <x-slot:heading>New Product</x-slot:heading>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" x-data="{
        selectedColors: [],
        selectedSizes: [],
        variants: [],
    
        generateVariants() {
            const existing = {};
            this.variants.forEach(v => {
                existing[v.color_id + '_' + v.size_id] = v;
            });
    
            this.variants = [];
            this.selectedColors.forEach(colorId => {
                this.selectedSizes.forEach(sizeId => {
                    const key = colorId + '_' + sizeId;
                    this.variants.push(existing[key] || {
                        color_id: colorId,
                        size_id: sizeId,
                        sku: '',
                        stock_quantity: 0,
                        price_override: '',
                    });
                });
            });
        },
    
        colorName(id) {
            const el = document.querySelector(`[data-color-id='${id}']`);
            return el ? el.dataset.colorName : id;
        },
    
        sizeName(id) {
            const el = document.querySelector(`[data-size-id='${id}']`);
            return el ? el.dataset.sizeName : id;
        }
    }">
        @csrf

        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Main Info --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Basic Info --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold mb-5">Product Info</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Product Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="e.g. Classic Leather Jacket"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]
                                          @error('name') border-red-400 @enderror">
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}"
                                placeholder="auto-generated from name if left empty"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono
                                          focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                            <textarea name="description" rows="4" placeholder="Product description..."
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none
                                             focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Base Price</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" name="base_price" value="{{ old('base_price') }}"
                                        step="0.01" min="0" placeholder="0.00"
                                        class="w-full border border-gray-200 rounded-xl pl-7 pr-4 py-2.5 text-sm
                                                  focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]
                                                  @error('base_price') border-red-400 @enderror">
                                </div>
                                @error('base_price')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subcategory</label>
                                <select name="category_id"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                               focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]
                                               @error('category_id') border-red-400 @enderror">
                                    <option value="">Select subcategory</option>
                                    @foreach ($genders as $gender)
                                        <optgroup label="{{ $gender->name }}">
                                            @foreach ($gender->children as $category)
                                        <optgroup label="  {{ $category->name }}">
                                            @foreach ($category->children as $sub)
                                                <option value="{{ $sub->id }}"
                                                    {{ old('category_id') == $sub->id ? 'selected' : '' }}>
                                                    {{ $sub->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fit</label>
                                <select name="fit_id"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                               focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                                    <option value="">Select fit</option>
                                    @foreach ($fits as $fit)
                                        <option value="{{ $fit->id }}"
                                            {{ old('fit_id') == $fit->id ? 'selected' : '' }}>
                                            {{ ucfirst($fit->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Brand</label>
                                <select name="brand_id"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                               focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                                    <option value="">No brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Variant Builder --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold mb-1">Variants</h2>
                    <p class="text-xs text-gray-400 mb-5">Select colours and sizes, then generate the variant matrix.
                    </p>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        {{-- Colors --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Colours</label>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach ($colors as $color)
                                    <label class="flex items-center gap-2.5 cursor-pointer group"
                                        data-color-id="{{ $color->id }}"
                                        data-color-name="{{ ucfirst($color->name) }}">
                                        <input type="checkbox" :value="{{ $color->id }}" x-model="selectedColors"
                                            @change="generateVariants()"
                                            class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E]">
                                        <div class="w-4 h-4 rounded-full border border-gray-200 flex-shrink-0"
                                            style="background-color: {{ $color->hex_code ?? '#ccc' }}">
                                        </div>
                                        <span class="text-sm text-gray-700">{{ ucfirst($color->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Sizes --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Sizes</label>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach ($sizes as $size)
                                    <label class="flex items-center gap-2.5 cursor-pointer"
                                        data-size-id="{{ $size->id }}" data-size-name="{{ $size->name }}">
                                        <input type="checkbox" :value="{{ $size->id }}" x-model="selectedSizes"
                                            @change="generateVariants()"
                                            class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E]">
                                        <span class="text-sm text-gray-700">{{ $size->name }}</span>
                                        <span class="text-xs text-gray-400">{{ ucfirst($size->type) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Variant Matrix --}}
                    <div x-show="variants.length > 0" style="display:none;">
                        <div class="border border-gray-100 rounded-xl overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 uppercase">
                                            Colour</th>
                                        <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 uppercase">
                                            Size</th>
                                        <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 uppercase">
                                            SKU</th>
                                        <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 uppercase">
                                            Stock</th>
                                        <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 uppercase">
                                            Price Override</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(variant, i) in variants" :key="i">
                                        <tr>
                                            <td class="px-4 py-2.5 text-gray-600" x-text="colorName(variant.color_id)">
                                            </td>
                                            <td class="px-4 py-2.5 text-gray-600" x-text="sizeName(variant.size_id)">
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="text" :name="`variants[${i}][sku]`"
                                                    x-model="variant.sku" placeholder="SKU-001"
                                                    class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs
                                                              focus:outline-none focus:ring-1 focus:ring-[#C85C6E] font-mono">
                                                <input type="hidden" :name="`variants[${i}][color_id]`"
                                                    :value="variant.color_id">
                                                <input type="hidden" :name="`variants[${i}][size_id]`"
                                                    :value="variant.size_id">
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="number" :name="`variants[${i}][stock_quantity]`"
                                                    x-model="variant.stock_quantity" min="0" placeholder="0"
                                                    class="w-20 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs
                                                              focus:outline-none focus:ring-1 focus:ring-[#C85C6E]">
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <div class="relative w-28">
                                                    <span
                                                        class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                                                    <input type="number" :name="`variants[${i}][price_override]`"
                                                        x-model="variant.price_override" step="0.01"
                                                        min="0" placeholder="Base price"
                                                        class="w-full border border-gray-200 rounded-lg pl-5 pr-2.5 py-1.5 text-xs
                                                                  focus:outline-none focus:ring-1 focus:ring-[#C85C6E]">
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p x-show="variants.length === 0" class="text-sm text-gray-400 text-center py-4">
                        Select at least one colour and one size above
                    </p>
                </div>

                {{-- Images --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold mb-1">Images</h2>
                    <p class="text-xs text-gray-400 mb-5">
                        These are general product images — shown before a variant is selected.
                        Colour-specific images can be added after the product is created.
                    </p>

                    <div x-data="{ dragging: false, files: [] }" @dragover.prevent="dragging = true" @dragleave="dragging = false"
                        @drop.prevent="
             dragging = false;
             files = Array.from($event.dataTransfer.files);
             // push files into the actual input
             const dt = new DataTransfer();
             files.forEach(f => dt.items.add(f));
             document.getElementById('images').files = dt.files;
         "
                        :class="dragging ? 'border-[#C85C6E] bg-rose-50' : 'border-gray-200 bg-gray-50'"
                        class="border-2 border-dashed rounded-xl p-8 text-center transition-colors cursor-pointer"
                        onclick="document.getElementById('images').click()">

                        <svg class="w-8 h-8 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm text-gray-500">
                            Drop images here or <span class="text-[#C85C6E]">browse</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 5MB · First image becomes primary</p>

                        <input type="file" id="images" name="images[]" multiple accept="image/*"
                            class="hidden" @change="files = Array.from($event.target.files)">

                        <div class="mt-3 space-y-1 text-left" x-show="files.length > 0">
                            <template x-for="(f, i) in files" :key="i">
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span x-text="f.name"></span>
                                    <span x-show="i === 0" class="text-[#C85C6E] font-medium">(primary)</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-semibold text-sm mb-4">Publish</h3>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E]">
                        <span class="text-sm text-gray-700">Active (visible in store)</span>
                    </label>
                    <div class="mt-5 space-y-2.5">
                        <button type="submit"
                            class="w-full bg-[#1C1C1C] text-white py-2.5 rounded-xl text-sm font-medium
                                       hover:bg-[#C85C6E] transition-colors">
                            Create Product
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="block w-full text-center py-2.5 text-sm text-gray-500 hover:text-[#1C1C1C] transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin.layout>
