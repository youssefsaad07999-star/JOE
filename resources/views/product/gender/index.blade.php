<x-layout>
    <div class="font-bold">
        {{-- <div class="flex gap-5">

            @foreach($genders as $gender)
            <a href="{{ route(" $gender->slug.index") }}">
                <h1>
                    {{ $gender->name }}
                </h1>
            </a>
            @endforeach

        </div> --}}

        <div class="flex gap-5">
            @foreach($categories as $category)
                <a href="{{ route("$currentGender->slug.category.show", $category) }}">
                    <h3>{{ $category->name }}</h3>
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