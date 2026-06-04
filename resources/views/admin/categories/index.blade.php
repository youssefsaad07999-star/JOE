<x-admin.layout>
    <x-slot:heading>Categories</x-slot:heading>
    <x-slot:subheading>Manage genders, categories and subcategories</x-slot:subheading>

    <x-slot:actions>
        <button @click="$dispatch('open-add-category')"
            class="bg-[#C85C6E] text-white px-4 py-2 rounded-xl text-sm font-medium
                       hover:bg-[#b54e60] transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Category
        </button>
    </x-slot:actions>

    {{-- Add Modal --}}
    <div x-data="{
        open: false,
        name: '',
        slug: '',
        parentId: '',
        depth: 'gender',
        autoSlug: true,
    
        computeSlug() {
            if (this.autoSlug) {
                this.slug = this.name
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            }
        },
    
        setDepthFromParent(parentId, allCategories) {
            if (!parentId) { this.depth = 'gender'; return; }
            const parent = allCategories.find(c => c.id == parentId);
            if (!parent) return;
            this.depth = parent.depth === 'gender' ? 'category' : 'subcategory';
        }
    }" @open-add-category.window="open = true; name=''; slug=''; parentId=''; depth='gender'">

        <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" @click.self="open = false"
            class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" style="display:none;">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-['Cormorant_Garamond'] text-xl font-semibold">Add Category</h3>
                    <button @click="open = false" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Parent</label>
                        <select name="parent_id" x-model="parentId"
                            @change="setDepthFromParent($event.target.value, {{ $allCategories->toJson() }})"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                            <option value="">None (Top-level / Gender)</option>
                            @foreach ($genders as $gender)
                                <optgroup label="{{ $gender->name }}">
                                    <option value="{{ $gender->id }}">{{ $gender->name }}</option>
                                    @foreach ($gender->children as $cat)
                                        <option value="{{ $cat->id }}">
                                            &nbsp;&nbsp;{{ $cat->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <input type="text" name="name" x-model="name" @input="computeSlug()"
                            placeholder="e.g. Jackets"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Slug</label>
                        <input type="text" name="slug" x-model="slug" @focus="autoSlug = false"
                            placeholder="auto-generated"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono
                                      focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                    </div>

                    <input type="hidden" name="depth" :value="depth">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" value="0" min="0"
                            class="w-24 border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="flex-1 bg-[#1C1C1C] text-white py-2.5 rounded-xl text-sm font-medium
                                       hover:bg-[#C85C6E] transition-colors">
                            Create
                        </button>
                        <button type="button" @click="open = false"
                            class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm
                                       hover:border-gray-400 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Category Tree --}}
    <div class="space-y-4">
        @foreach ($genders as $gender)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                {{-- Gender row --}}
                <div class="flex items-center justify-between px-6 py-4 bg-[#1C1C1C] text-white">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <div>
                            <span class="font-medium">{{ $gender->name }}</span>
                            <span class="text-xs text-gray-400 ml-2 font-mono">{{ $gender->slug }}</span>
                        </div>
                        <span class="text-[10px] uppercase tracking-wider bg-white/10 px-2 py-0.5 rounded">
                            Gender
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">
                            {{ $gender->children->count() }} categories
                        </span>
                        @include('admin.categories._row_actions', ['category' => $gender])
                    </div>
                </div>

                {{-- Category rows --}}
                @foreach ($gender->children as $category)
                    <div class="border-t border-gray-100">
                        <div
                            class="flex items-center justify-between px-6 py-3 bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3 pl-6">
                                <div class="w-px h-4 bg-gray-300"></div>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span class="text-sm font-medium">{{ $category->name }}</span>
                                <span class="text-xs text-gray-400 font-mono">{{ $category->slug }}</span>
                                <span
                                    class="text-[10px] uppercase tracking-wider bg-blue-100 text-blue-600 px-2 py-0.5 rounded">
                                    Category
                                </span>
                                @if (!$category->is_active)
                                    <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded">Hidden</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400">
                                    {{ $category->children->count() }} subcategories
                                </span>
                                @include('admin.categories._row_actions', ['category' => $category])
                            </div>
                        </div>

                        {{-- Subcategory rows --}}
                        @foreach ($category->children as $sub)
                            <div
                                class="flex items-center justify-between px-6 py-2.5
                                        border-t border-gray-50 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3 pl-16">
                                    <div class="w-px h-3 bg-gray-200"></div>
                                    <span class="text-sm text-gray-700">{{ $sub->name }}</span>
                                    <span class="text-xs text-gray-400 font-mono">{{ $sub->slug }}</span>
                                    <span
                                        class="text-[10px] uppercase tracking-wider bg-[#C85C6E]/10 text-[#C85C6E] px-2 py-0.5 rounded">
                                        Sub
                                    </span>
                                    @if (!$sub->is_active)
                                        <span
                                            class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded">Hidden</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400">
                                        {{ $sub->products_count }} products
                                    </span>
                                    @include('admin.categories._row_actions', ['category' => $sub])
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</x-admin.layout>
