<x-layout>

    {{-- Hero --}}
    <section class="bg-[#1C1C1C] text-white py-24 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-4">Our Story</p>
            <h1 class="font-['Cormorant_Garamond'] text-6xl md:text-7xl font-light leading-tight">
                Fashion with<br><em class="italic text-[#C85C6E]">Purpose</em>
            </h1>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-6 py-20">

        {{-- Mission --}}
        <div class="grid md:grid-cols-2 gap-16 items-center mb-20">
            <div>
                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-4">Who We Are</p>
                <h2 class="font-['Cormorant_Garamond'] text-4xl font-light mb-6 leading-tight">
                    We believe style should be effortless
                </h2>
                <p class="text-gray-600 font-light leading-relaxed mb-4">
                    JOE Store was born from a simple idea: great fashion shouldn't be complicated. We curate pieces for
                    men and women who want to look and feel their best — without spending hours figuring out how.
                </p>
                <p class="text-gray-600 font-light leading-relaxed">
                    From everyday essentials to standout pieces, every item in our collection is chosen with intention,
                    quality, and wearability in mind.
                </p>
            </div>
            <div
                class="aspect-[4/3] rounded-3xl bg-gradient-to-br from-[#C85C6E]/20 via-stone-200 to-stone-300 overflow-hidden">
                <div class="w-full h-full flex items-center justify-center">
                    <span class="font-['Cormorant_Garamond'] text-8xl font-light text-white/50">JOE</span>
                </div>
            </div>
        </div>

        {{-- Values --}}
        <div class="mb-20">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-3 text-center">What Drives Us
            </p>
            <h2 class="font-['Cormorant_Garamond'] text-4xl font-light text-center mb-12">Our Values</h2>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                        ['icon' => '✦', 'title' => 'Quality First', 'desc' => 'Every piece we carry is selected for its material quality, craftsmanship, and longevity. We believe in fashion that lasts.'],
                        ['icon' => '◆', 'title' => 'Inclusive Style', 'desc' => 'Fashion is for everyone. Our collections are designed to celebrate diverse body types, personal styles, and lifestyles.'],
                        ['icon' => '●', 'title' => 'Honest Pricing', 'desc' => 'Great style shouldn\'t break the bank. We price our pieces fairly and transparently, with no hidden fees or surprises.'],
                    ] as $value)
                    <div class="bg-white rounded-2xl p-7 shadow-sm">
                        <span class="text-[#C85C6E] text-2xl mb-4 block">{{ $value['icon'] }}</span>
                        <h3 class="font-semibold text-lg mb-3">{{ $value['title'] }}</h3>
                        <p class="text-gray-600 font-light leading-relaxed text-sm">{{ $value['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Stats --}}
        <div class="bg-[#1C1C1C] text-white rounded-3xl p-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @foreach([
                        ['number' => '5K+', 'label' => 'Happy Customers'],
                        ['number' => '200+', 'label' => 'Products'],
                        ['number' => '30', 'label' => 'Day Returns'],
                        ['number' => '4.9★', 'label' => 'Average Rating'],
                    ] as $stat)
                    <div>
                        <p class="font-['Cormorant_Garamond'] text-4xl font-light text-[#C85C6E]">{{ $stat['number'] }}</p>
                        <p class="text-gray-400 text-sm mt-1 font-light">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-layout>