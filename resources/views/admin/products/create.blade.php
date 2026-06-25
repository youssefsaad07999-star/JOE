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

        {{-- Master Layout Column Control --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main Info Column Stack --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Basic Details Card --}}
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold text-gray-900 mb-5">Product Info</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Product Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="e.g. Classic Leather Jacket"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white
                                       focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]
                                       @error('name') border-red-400 focus:ring-red-200 focus:border-red-400 @enderror">
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}"
                                placeholder="auto-generated from name if left empty"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono bg-white
                                       focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                            <textarea name="description" rows="4" placeholder="Product description..."
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none bg-white
                                       focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">{{ old('description') }}</textarea>
                        </div>

                        {{-- Pricing and Collection Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Base Price</label>
                                <div class="relative shadow-sm rounded-xl">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" name="base_price" value="{{ old('base_price') }}"
                                        step="0.01" min="0" placeholder="0.00"
                                        class="w-full border border-gray-200 rounded-xl pl-8 pr-4 py-2.5 text-sm bg-white
                                               focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]
                                               @error('base_price') border-red-400 focus:ring-red-200 focus:border-red-400 @enderror">
                                </div>
                                @error('base_price')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subcategory</label>
                                <select name="category_id"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white shadow-sm
                                           focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]
                                           @error('category_id') border-red-400 focus:ring-red-200 focus:border-red-400 @enderror">
                                    <option value="">Select subcategory</option>
                                    @foreach ($genders as $gender)
                                        <optgroup label="{{ $gender->name }}">
                                            @foreach ($gender->children as $category)
                                        <optgroup label="&nbsp;&nbsp;{{ $category->name }}">
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

                        {{-- Metadata Styling Attributes Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fit</label>
                                <select name="fit_id"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white shadow-sm
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
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white shadow-sm
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

                {{-- Variant Attribute Matrix Generator --}}
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold text-gray-900 mb-1">Variants</h2>
                    <p class="text-xs text-gray-400 mb-5">Select colours and sizes, then generate the variant matrix.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        {{-- Color Options Multi-Selector --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Colours</label>
                            <div
                                class="space-y-2 max-h-48 overflow-y-auto pr-2 border border-gray-50 rounded-xl p-2 bg-gray-50/30">
                                @foreach ($colors as $color)
                                    <label class="flex items-center gap-2.5 cursor-pointer group select-none"
                                        data-color-id="{{ $color->id }}"
                                        data-color-name="{{ ucfirst($color->name) }}">
                                        <input type="checkbox" :value="{{ $color->id }}" x-model="selectedColors"
                                            @change="generateVariants()"
                                            class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E] transition-colors">
                                        <div class="w-4 h-4 rounded-full border border-gray-300 flex-shrink-0 shadow-sm"
                                            style="background-color: {{ $color->hex_code ?? '#ccc' }}">
                                        </div>
                                        <span
                                            class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">{{ ucfirst($color->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Size Options Multi-Selector --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Sizes</label>
                            <div
                                class="space-y-2 max-h-48 overflow-y-auto pr-2 border border-gray-50 rounded-xl p-2 bg-gray-50/30">
                                @foreach ($sizes as $size)
                                    <label class="flex items-center gap-2.5 cursor-pointer group select-none"
                                        data-size-id="{{ $size->id }}" data-size-name="{{ $size->name }}">
                                        <input type="checkbox" :value="{{ $size->id }}" x-model="selectedSizes"
                                            @change="generateVariants()"
                                            class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E] transition-colors">
                                        <span
                                            class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">{{ $size->name }}</span>
                                        <span
                                            class="text-[10px] bg-gray-200/60 text-gray-500 px-1.5 py-0.5 rounded-md font-medium uppercase tracking-wider ml-auto">{{ $size->type }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Matrix Formulation Panel --}}
                    <div x-show="variants.length > 0" x-transition style="display:none;">
                        <div class="overflow-x-auto w-full border border-gray-100 rounded-xl shadow-sm">
                            <table class="w-full text-sm min-w-[680px] table-auto">
                                <thead class="bg-gray-50/80">
                                    <tr>
                                        <th
                                            class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Colour</th>
                                        <th
                                            class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Size</th>
                                        <th
                                            class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            SKU</th>
                                        <th
                                            class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Stock</th>
                                        <th
                                            class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Price Override</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <template x-for="(variant, i) in variants" :key="i">
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-4 py-3 text-gray-700 font-medium whitespace-nowrap"
                                                x-text="colorName(variant.color_id)"></td>
                                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap"
                                                x-text="sizeName(variant.size_id)"></td>
                                            <td class="px-4 py-3">
                                                <input type="text" :name="`variants[${i}][sku]`"
                                                    x-model="variant.sku" placeholder="SKU-001"
                                                    class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs bg-white
                                                           focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E] font-mono">
                                                <input type="hidden" :name="`variants[${i}][color_id]`"
                                                    :value="variant.color_id">
                                                <input type="hidden" :name="`variants[${i}][size_id]`"
                                                    :value="variant.size_id">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" :name="`variants[${i}][stock_quantity]`"
                                                    x-model="variant.stock_quantity" min="0" placeholder="0"
                                                    class="w-24 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs bg-white
                                                           focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="relative rounded-lg shadow-sm w-32">
                                                    <span
                                                        class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                                                    <input type="number" :name="`variants[${i}][price_override]`"
                                                        x-model="variant.price_override" step="0.01"
                                                        min="0" placeholder="Base price"
                                                        class="w-full border border-gray-200 rounded-lg pl-6 pr-2.5 py-1.5 text-xs bg-white
                                                               focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div x-show="variants.length === 0"
                        class="text-sm text-gray-400 text-center py-6 border border-dashed border-gray-100 rounded-xl bg-gray-50/40">
                        Select at least one colour and one size above to generate options
                    </div>
                </div>

                {{-- Asset File Media Loading Area --}}
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100">
                    <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold text-gray-900 mb-1">Images</h2>
                    <p class="text-xs text-gray-400 mb-5">
                        These are general product images — shown before a variant is selected.
                        Colour-specific images can be added after the product is created.
                    </p>

                    <div x-data="{ dragging: false, files: [] }" @dragover.prevent="dragging = true" @dragleave="dragging = false"
                        @drop.prevent="
                            dragging = false;
                            files = Array.from($event.dataTransfer.files);
                            const dt = new DataTransfer();
                            files.forEach(f => dt.items.add(f));
                            document.getElementById('images').files = dt.files;
                        "
                        :class="dragging ? 'border-[#C85C6E] bg-rose-50/40' : 'border-gray-200 bg-gray-50/50'"
                        class="border-2 border-dashed rounded-xl p-6 sm:p-8 text-center transition-all cursor-pointer hover:bg-gray-50/80"
                        onclick="document.getElementById('images').click()">

                        <svg class="w-8 h-8 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm text-gray-600 font-medium">
                            Drop images here or <span
                                class="text-[#C85C6E] underline decoration-wavy decoration-1">browse</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">PNG, JPG up to 5MB · First image becomes
                            primary fallback</p>

                        <input type="file" id="images" name="images[]" multiple accept="image/*"
                            class="hidden" @change="files = Array.from($event.target.files)">

                        {{-- Local Image Stack Feedback List --}}
                        <div class="mt-4 space-y-1.5 text-left max-w-md mx-auto" x-show="files.length > 0"
                            x-transition>
                            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Selected
                                Uploads</div>
                            <template x-for="(f, i) in files" :key="i">
                                <div
                                    class="flex items-center gap-2 text-xs text-gray-600 bg-white border border-gray-100 p-2 rounded-lg shadow-xs truncate">
                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="truncate flex-1" x-text="f.name"></span>
                                    <span x-show="i === 0"
                                        class="text-[10px] bg-rose-50 text-[#C85C6E] px-1.5 py-0.5 rounded font-semibold flex-shrink-0">Primary</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sticky/Sidebar Control Column --}}
            <div class="space-y-5">
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 sticky top-6">
                    <h3 class="font-semibold text-sm text-gray-900 mb-4">Publish Settings</h3>

                    <label
                        class="flex items-center gap-3 cursor-pointer select-none group pb-4 border-b border-gray-50">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E]">
                        <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Active (visible
                            in catalog)</span>
                    </label>

                    <div class="mt-4 space-y-2">
                        <button type="submit"
                            class="w-full bg-[#1C1C1C] text-white py-2.5 rounded-xl text-sm font-medium
                                   hover:bg-[#C85C6E] transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/40">
                            Create Product
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="block w-full text-center py-2.5 rounded-xl text-sm text-gray-500 font-medium hover:bg-gray-50 hover:text-gray-900 transition-all">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin.layout>
