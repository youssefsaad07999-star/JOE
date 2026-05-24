<x-layout>
    <div class="min-h-[calc(100vh-64px)] flex">

        {{-- Left Panel --}}
        <div class="hidden lg:flex w-1/2 bg-[#1C1C1C] items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-30"
                style="background-image: radial-gradient(circle at 70% 30%, #C85C6E 0%, transparent 50%)">
            </div>
            <div class="relative text-center px-12">
                <span class="font-['Cormorant_Garamond'] text-6xl font-light text-white tracking-widest">JOE</span>
                <p class="text-gray-400 mt-4 font-light text-lg leading-relaxed">
                    Join thousands of fashion lovers.<br>Create your free account today.
                </p>
                <div class="flex justify-center gap-8 mt-10">
                    @foreach(['Free Returns', 'Style Tips', 'Early Access'] as $perk)
                        <div class="text-center">
                            <div
                                class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center mx-auto mb-2">
                                <span class="text-[#C85C6E] text-sm">✦</span>
                            </div>
                            <p class="text-gray-400 text-xs font-light">{{ $perk }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-[#F7F3EE]">
            <div class="w-full max-w-md">

                <div class="mb-8">
                    <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">Get Started</p>
                    <h1 class="font-['Cormorant_Garamond'] text-4xl font-light">Create Account</h1>
                </div>

                <form action="/register" method="POST" class="space-y-4">
                    @csrf
                    <x-form.field name="name" title="Full Name" placeholder="Your name" />
                    <x-form.field name="age" title="Age" type="number" placeholder="25" />
                    <x-form.field name="email" title="Email Address" type="email" placeholder="you@example.com" />

                    <div>
                        <x-form.field name="password" title="Password" type="password"
                            placeholder="Min. 8 characters" />
                        <div class="mt-2 flex gap-1.5" id="password-strength">
                            @for($i = 0; $i < 4; $i++)
                                <div class="h-1 flex-1 rounded-full bg-gray-200"></div>
                            @endfor
                        </div>
                    </div>

                    <x-form.field name="password_confirmation" title="Confirm Password" type="password"
                        placeholder="Repeat your password" />

                    <label class="flex items-start gap-3 cursor-pointer pt-1">
                        <input type="checkbox" name="terms"
                            class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E] mt-0.5">
                        <span class="text-sm text-gray-600">
                            I agree to the
                            <a href="#" class="text-[#C85C6E] underline">Terms of Service</a> and
                            <a href="#" class="text-[#C85C6E] underline">Privacy Policy</a>
                        </span>
                    </label>

                    <button type="submit" class="w-full bg-[#1C1C1C] text-white py-4 rounded-full font-medium
                                   hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide mt-2">
                        Create Account
                    </button>
                </form>

                <p class="text-center text-gray-500 text-sm mt-6">
                    Already have an account?
                    <a href="/login" class="text-[#C85C6E] font-medium hover:underline ml-1">Sign in</a>
                </p>

            </div>
        </div>
    </div>
</x-layout>