<x-layout>
    <div class="flex items-center justify-center mt-10 ">
        <div class="bg-gray-700 p-8 rounded-lg shadow-lg w-full max-w-lg ">
            <h2 class="text-2xl font-bold text-center mb-6 text-gray-200">Contact Us</h2>
            <p class="text-center text-black mb-6">
                We'd love to hear from you! Fill out the form below and we'll get back to you soon.
            </p>
            <form class="space-y-4">
                <div>
                    <label class="block text-white mb-1">Full Name</label>
                    <input type="text"
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="John Doe">
                </div>
                <div>
                    <label class="block text-white mb-1">Email</label>
                    <input type="email"
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="you@example.com">
                </div>
                <div>
                    <label class="block text-white mb-1">Message</label>
                    <textarea
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        rows="4" placeholder="Write your message..."></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-pink-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Send
                    Message</button>
            </form>
        </div>
    </div>

</x-layout>