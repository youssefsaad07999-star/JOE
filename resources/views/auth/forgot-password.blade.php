<x-layout>
    <div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-6 py-12 bg-[#F7F3EE]">
        <div class="w-full max-w-md">

            <div class="mb-8">
                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">
                    Account
                </p>
                <h1 class="font-['Cormorant_Garamond'] text-4xl font-light">Forgot Password</h1>
                <p class="text-gray-500 text-sm mt-2 font-light">
                    Enter your email and we'll send you a link to reset your password.
                </p>
            </div>

            @session('status')
                <div
                    class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700
                            rounded-xl px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $value }}
                </div>
            @endsession
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <x-form.field name="email" title="Email Address" type="email" placeholder="you@example.com" />

                <button type="submit"
                    class="w-full bg-[#1C1C1C] text-white py-4 rounded-full font-medium
                               hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide">
                    Send Reset Link
                </button>
            </form>

            <p class="text-center text-gray-500 text-sm mt-6">
                Remember your password?
                <a href="{{ route('login') }}" class="text-[#C85C6E] font-medium hover:underline ml-1">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</x-layout>
