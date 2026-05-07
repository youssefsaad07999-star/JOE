<x-layout>
    <div class="flex items-center justify-center mt-12">
        <div class="bg-gray-900 p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6 text-white-800">Sign In</h2>
            <form action="/login" method="POST" class="space-y-4">
                @csrf
                <div>
                    <x-form.field name="email" title="Email" placeholder="you@example.com" type="email" />
                    <x-form.field name="password" title="Password" placeholder="••••••••" type="password" value="" />

                </div>
                <button type="submit"
                    class="w-full bg-pink-600 text-white py-2 rounded-md hover:bg-pink-700 transition">Login</button>
            </form>
            <p class="text-center text-gray-500 mt-4">Don't have an account?
                <a href="/register" class="text-blue-600 hover:underline">Register</a>
            </p>
        </div>
    </div>

</x-layout>