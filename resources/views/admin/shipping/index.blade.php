<x-admin.layout>
    <x-slot:heading>Shipping Methods</x-slot:heading>
    <x-slot:subheading>Configure shipping options shown at checkout</x-slot:subheading>

    <x-slot:actions>
        <button @click="$dispatch('open-add-shipping')"
            class="bg-[#C85C6E] text-white px-4 py-2 rounded-xl text-sm font-medium
                       hover:bg-[#b54e60] transition-colors flex items-center gap-2 w-full sm:w-auto justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Method
        </button>
    </x-slot:actions>

    {{-- Add Modal --}}
    <div x-data="{ open: false }" @open-add-shipping.window="open = true">

        <div x-show="open" x-transition @click.self="open = false"
            class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" style="display:none;">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[calc(100vh-2rem)] flex flex-col"
                @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                    <h3 class="font-['Cormorant_Garamond'] text-xl font-semibold">Add Shipping Method</h3>
                    <button @click="open = false" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.shipping.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <input type="text" name="name" placeholder="Standard Shipping"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Delivery Time</label>
                        <input type="text" name="delivery_time" placeholder="5–7 business days"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                            <input type="number" name="price" step="0.01" min="0" placeholder="9.99"
                                class="w-full border border-gray-200 rounded-xl pl-7 pr-4 py-2.5 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E]">
                        </div>
                    </div>

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

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[800px] table-auto">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/75">
                        <th
                            class="text-left px-4 sm:px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Method</th>
                        <th
                            class="text-left px-4 sm:px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Delivery Time</th>
                        <th
                            class="text-right px-4 sm:px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Price</th>
                        <th
                            class="text-center px-4 sm:px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Sort</th>
                        <th
                            class="text-center px-4 sm:px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            Active</th>
                        <th class="px-4 sm:px-6 py-3.5 w-[140px]"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($shippingMethods as $method)
                        <tr class="hover:bg-gray-50/50 transition-colors" x-data="{
                            editing: false,
                            name: '{{ addslashes($method->name) }}',
                            delivery: '{{ addslashes($method->delivery_time) }}',
                            price: '{{ $method->price }}',
                            sort: '{{ $method->sort_order }}',
                        
                            // Explicit reset function to undo unsaved typing actions
                            resetForm() {
                                this.name = '{{ addslashes($method->name) }}';
                                this.delivery = '{{ addslashes($method->delivery_time) }}';
                                this.price = '{{ $method->price }}';
                                this.sort = '{{ $method->sort_order }}';
                                this.editing = false;
                            }
                        }">

                            <td class="px-4 sm:px-6 py-4 vertical-align-middle">
                                <div x-show="!editing">
                                    <p class="text-sm font-medium text-gray-900">{{ $method->name }}</p>
                                </div>
                                <input x-show="editing" type="text" x-model="name"
                                    class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-sm w-44 max-w-full
                                              focus:outline-none focus:ring-1 focus:ring-[#C85C6E]"
                                    style="display:none;">
                            </td>

                            <td class="px-4 sm:px-6 py-4 vertical-align-middle">
                                <span x-show="!editing"
                                    class="text-sm text-gray-500">{{ $method->delivery_time }}</span>
                                <input x-show="editing" type="text" x-model="delivery"
                                    class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-sm w-40 max-w-full
                                              focus:outline-none focus:ring-1 focus:ring-[#C85C6E]"
                                    style="display:none;">
                            </td>

                            <td class="px-4 sm:px-6 py-4 text-right vertical-align-middle">
                                <span x-show="!editing" class="text-sm font-semibold text-gray-900">
                                    ${{ number_format($method->price, 2) }}
                                </span>
                                <div x-show="editing" class="relative w-24 ml-auto" style="display:none;">
                                    <span
                                        class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                                    <input type="number" x-model="price" step="0.01" min="0"
                                        class="w-full border border-gray-200 rounded-lg pl-5 pr-2.5 py-1.5 text-sm
                                                  focus:outline-none focus:ring-1 focus:ring-[#C85C6E]">
                                </div>
                            </td>

                            <td class="px-4 sm:px-6 py-4 text-center vertical-align-middle">
                                <span x-show="!editing"
                                    class="text-sm text-gray-500">{{ $method->sort_order }}</span>
                                <input x-show="editing" type="number" x-model="sort" min="0"
                                    class="w-16 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center
                                              focus:outline-none focus:ring-1 focus:ring-[#C85C6E] mx-auto"
                                    style="display:none;">
                            </td>

                            <td class="px-4 sm:px-6 py-4 text-center vertical-align-middle">
                                <form action="{{ route('admin.shipping.update', $method) }}" method="POST"
                                    class="inline-block">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="toggle_active" value="1">
                                    <button type="submit"
                                        class="relative inline-flex h-5 w-9 rounded-full transition-colors focus:outline-none
                                                   {{ $method->is_active ? 'bg-[#C85C6E]' : 'bg-gray-200' }}">
                                        <span
                                            class="w-4 h-4 bg-white rounded-full shadow transform transition-transform mt-0.5
                                                     {{ $method->is_active ? 'translate-x-4' : 'translate-x-0.5' }}">
                                        </span>
                                    </button>
                                </form>
                            </td>

                            <td class="px-4 sm:px-6 py-4 vertical-align-middle">
                                <div x-show="!editing" class="flex items-center justify-end gap-3">
                                    <button @click="editing = true"
                                        class="text-xs text-gray-400 hover:text-[#C85C6E] transition-colors font-medium">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.shipping.destroy', $method) }}" method="POST"
                                        x-data class="inline"
                                        @submit.prevent="confirm('Delete this shipping method?') && $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-xs text-red-400 hover:text-red-600 transition-colors font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>

                                <div x-show="editing" class="flex items-center justify-end gap-2"
                                    style="display:none;">
                                    <form action="{{ route('admin.shipping.update', $method) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="name" :value="name">
                                        <input type="hidden" name="delivery_time" :value="delivery">
                                        <input type="hidden" name="price" :value="price">
                                        <input type="hidden" name="sort_order" :value="sort">
                                        <button type="submit"
                                            class="text-xs bg-[#1C1C1C] text-white px-2.5 py-1 rounded-lg
                                                       hover:bg-[#C85C6E] transition-colors font-medium">
                                            Save
                                        </button>
                                    </form>
                                    <button type="button" @click="resetForm()"
                                        class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1 font-medium">
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-400 text-sm bg-white">
                                No shipping methods yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin.layout>
