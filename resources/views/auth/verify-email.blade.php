<x-layout>
    <div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-6 py-12 bg-[#F7F3EE]">
        <div class="w-full max-w-md">

            <div class="mb-8 text-center">
                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">
                    Security
                </p>
                <h1 class="font-['Cormorant_Garamond'] text-4xl font-light">Verify Your Email</h1>
                <p class="text-gray-500 text-sm mt-3 font-light leading-relaxed">
                    First you need to verify your email to have updates!, could you verify your email address by
                    clicking the
                    link we just emailed to you?
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div
                    class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <div class="space-y-4">
                {{-- Resend Button Form --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-[#1C1C1C] text-white py-4 rounded-full font-medium hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide">
                        Resend Verification Email
                    </button>
                </form>

                {{-- Logout Button so they aren't trapped --}}
                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-[#C85C6E] underline font-light">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layout>
