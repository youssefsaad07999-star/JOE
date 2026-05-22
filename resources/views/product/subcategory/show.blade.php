<x-layout>
    <div>
        <div class="font-bold flex gap-5">
            @foreach($subcategories as $subcategory)
                <a href="{{ route("$gender.subcategory.show", [$category, $subcategory]) }}">
                    {{ $subcategory->name }}
                </a>
            @endforeach
        </div>
    </div>

    @forelse ($products as $product)
        <p>{{ $product->name }}</p>
    @empty
        <p class="text-center text-gray-500">No products available, stay tuned!.</p>
    @endforelse
</x-layout>