<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JOE</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-800 px-30  text-white flex flex-col min-h-screen pt-0">
  <x-layout.nav />
  <main class="flex-grow">

    {{ $slot }}

  </main>



  <x-layout.footer />
  @session('success')

    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
      x-transition.opacity.duration.300ms class="bg-green-200 px-4 py-3 fixed bottom-4 right-4 rounded-lg text-black">
      {{ $value }}
    </div>
  @endsession
</body>

</html>