{{-- Inline edit + toggle + delete actions for a category row --}}
<div class="flex items-center gap-2" x-data="{ editing: false, name: '{{ addslashes($category->name) }}', slug: '{{ $category->slug }}' }">

    {{-- Edit inline --}}
    <div x-show="!editing" class="flex items-center gap-2">
        <button @click="editing = true"
            class="text-xs text-gray-400 hover:text-[#C85C6E] transition-colors px-2 py-1
                       rounded-lg hover:bg-[#C85C6E]/10">
            Edit
        </button>

        {{-- Toggle active --}}
        <form action="{{ route('admin.categories.toggle', $category->id) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit"
                class="text-xs px-2 py-1 rounded-lg transition-colors
                           {{ $category->is_active ? 'text-emerald-600 hover:bg-emerald-50' : 'text-gray-400 hover:bg-gray-100' }}">
                {{ $category->is_active ? 'Active' : 'Hidden' }}
            </button>
        </form>

        {{-- Delete --}}
        @if (($category->children_count ?? $category->children->count()) === 0)
            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" x-data
                @submit.prevent="confirm('Delete {{ $category->name }}? This cannot be undone.') && $el.submit()">
                @csrf @method('DELETE')
                <button type="submit"
                    class="text-xs text-red-400 hover:text-red-600 transition-colors px-2 py-1
                               rounded-lg hover:bg-red-50">
                    Delete
                </button>
            </form>
        @endif
    </div>

    {{-- Inline edit form --}}
    <div x-show="editing" style="display:none;">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST"
            class="flex items-center gap-2">
            @csrf @method('PATCH')
            <input type="text" name="name" x-model="name"
                class="border border-gray-200 rounded-lg px-2.5 py-1 text-xs w-32
                          focus:outline-none focus:ring-1 focus:ring-[#C85C6E]">
            <input type="text" name="slug" x-model="slug"
                class="border border-gray-200 rounded-lg px-2.5 py-1 text-xs w-28 font-mono
                          focus:outline-none focus:ring-1 focus:ring-[#C85C6E]">
            <button type="submit"
                class="text-xs bg-[#1C1C1C] text-white px-2.5 py-1 rounded-lg hover:bg-[#C85C6E] transition-colors">
                Save
            </button>
            <button type="button" @click="editing = false" class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1">
                Cancel
            </button>
        </form>
    </div>
</div>
