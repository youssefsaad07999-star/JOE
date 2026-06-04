<x-layout>
    <div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-6 py-12 bg-[#F7F3EE]">
        <div class="w-full max-w-md">

            <div class="mb-8">
                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">
                    Account
                </p>
                <h1 class="font-['Cormorant_Garamond'] text-4xl font-light">Reset Password</h1>
                <p class="text-gray-500 text-sm mt-2 font-light">
                    Choose a new password for your account.
                </p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <x-form.field name="email" title="Email Address" type="email" placeholder="you@example.com"
                    :value="old('email', $request->email)" />

                <x-form.field name="password" title="New Password" type="password" placeholder="Min. 8 characters" />

                <x-form.field name="password_confirmation" title="Confirm Password" type="password"
                    placeholder="Repeat new password" />

                <button type="submit"
                    class="w-full bg-[#1C1C1C] text-white py-4 rounded-full font-medium
                               hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</x-layout>
