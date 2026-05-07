<nav class="h-20 bg-black text-white rounded-3xl">
    <div class="flex justify-between items-center">
        <div class="w-20 h-20">
            <img class="mx-1 rounded-3xl" src="images/Logo.jpeg">
        </div>
        <div class="flex items-center">
            <a href="/"
                class="px-5 hover:text-gray-400 py-2 @active('home') border-b-2 border-white @endactive">Home</a>
            <a href="/contact"
                class="px-5 hover:text-gray-400 py-2 @active('contact') border-b-2 border-white @endactive">Contact</a>
            <a href="/about"
                class="px-5 hover:text-gray-400 py-2 @active('about') border-b-2 border-white @endactive">About</a>

            @auth
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="px-5 border rounded-3xl py-1 mr-5 hover:bg-gray-800">Sign Out</button>
                </form>
            @else
                <a href="/login" class="px-5 border rounded-3xl py-1 ml-5 mr-2 hover:bg-gray-800">Sign in</a>
                <a href="/register" class="px-5 border rounded-3xl py-1 mr-5 hover:bg-gray-800">Sign Up</a>
            @endauth
        </div>
    </div>
</nav>