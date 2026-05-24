<x-layout>
    <div class="max-w-6xl mx-auto px-6 py-16">

        <div class="text-center mb-14">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-3">Get in Touch</p>
            <h1 class="font-['Cormorant_Garamond'] text-5xl md:text-6xl font-light">We'd Love to Hear<br>From You</h1>
        </div>

        <div class="grid md:grid-cols-2 gap-12">

            {{-- Contact Form --}}
            <div class="bg-white rounded-3xl p-8 shadow-sm">
                <form action="/contact" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <x-form.field name="first_name" title="First Name" placeholder="John" />
                        <x-form.field name="last_name" title="Last Name" placeholder="Doe" />
                    </div>
                    <x-form.field name="email" title="Email" type="email" placeholder="you@example.com" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject</label>
                        <select name="subject"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E] transition-colors text-gray-600">
                            <option value="">Select a topic</option>
                            <option value="order">Order Inquiry</option>
                            <option value="return">Returns & Exchanges</option>
                            <option value="product">Product Question</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
                        <textarea name="message" rows="5" placeholder="Tell us how we can help..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm placeholder-gray-400
                                         focus:outline-none focus:ring-2 focus:ring-[#C85C6E]/30 focus:border-[#C85C6E] transition-colors resize-none"></textarea>
                        <x-form.error name="message" />
                    </div>

                    <button type="submit" class="w-full bg-[#1C1C1C] text-white py-4 rounded-full font-medium
                                   hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide">
                        Send Message
                    </button>
                </form>
            </div>

            {{-- Info --}}
            <div class="space-y-8">
                <div>
                    <h2 class="font-['Cormorant_Garamond'] text-3xl font-light mb-6">Other Ways to Reach Us</h2>
                    <div class="space-y-5">
                        @foreach([
                                ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Email', 'value' => 'hello@joestore.com'],
                                ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'Phone', 'value' => '+1 (800) 123-4567'],
                                ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Address', 'value' => '123 Fashion Ave, New York, NY 10001'],
                            ] as $info)

                                                                    <div class="flex gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-[#F7F3EE] border border-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-[#C85C6E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="
                                        1.5" d="{{ $info['icon'] }}"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $info['label'] }}</p>
                                    <p class="text-[#1C1C1C] font-light mt-0.5">{{ $info['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Hours --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-semibold text-sm mb-4 uppercase tracking-wide text-gray-400">Support Hours</h3>
                    <div class="s
                               pace-y-2.5 text-sm">
                        @foreach(['Monday – Friday' => '9am – 6pm', 'Saturday' => '10am – 4pm', 'Sunday' => 'Closed'] as $day => $hours)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $day }}</span>
                                <span class="{{ $hours === 'Closed' ? 'text-gray-400' : 'font-medium' }}">{{ $hours }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Response time --}}
                <div class="bg-gradient-to-br from-[#C85C6E] to-[#8B6B8A] text-white rounded-2xl p-6">
                    <p class="font-['Cormorant_Garamond'] text-2xl font-light mb-2">Quick Response</p>
                    <p class="text-white/80 text-sm font-light">
                        We typically respond within 24 hours. For urgent matters, please call us directly.
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-layout>