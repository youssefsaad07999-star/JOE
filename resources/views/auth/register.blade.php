<x-layout>
    <div class="flex items-center justify-center mt-10">
        <div class="bg-gray-900 p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6 text-white-800">Create Account</h2>
            <form action="/register" method="POST" class="space-y-4">
                @csrf
                <x-form.field name="name" title="Name" placeholder="Write your name here!" />
                <x-form.field name="age" title="Age" placeholder="Write your age number here!" type="number" />
                <x-form.field name="email" title="Email" placeholder="you@example.com" type="email" />
                <x-form.field name="password" title="Password" placeholder="••••••••" type="password" />

                <button type="submit"
                    class="w-full bg-pink-600 text-white py-2 rounded-md hover:bg-pink-700 transition">Register</button>
            </form>
            <p class="text-center text-gray-500 mt-4">Already have an account?
                <a href="/login" class="text-pink-600 hover:underline">Login</a>
            </p>
        </div>
    </div>

</x-layout>