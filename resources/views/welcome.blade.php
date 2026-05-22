<x-layout>
    <div class="mx-3 mt-10">

        <div class="flex flex-col md:flex-row items-center justify-between gap-10">

            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl font-bold text-white mb-4">
                    Choose Your Style
                </h1>

                <p class="text-gray-300 mb-6">
                    Discover the latest trends in fashion for men and women.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    @foreach ($genders as $gender)
                        <a href="{{ route("$gender->slug.index") }}"
                            class="px-6 py-2 bg-pink-600 hover:bg-pink-700 rounded-3xl transition">
                            {{ $gender->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="flex-1">
                <img src="images/Landing2.png" class="w-full h-[400px] object-cover rounded-3xl shadow-lg">
            </div>

        </div>

    </div>

</x-layout>