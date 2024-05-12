<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' | ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="text-gray-700">
    <nav class="border-b">
      <div class="flex justify-between items-center container py-4 max-w-prose">
        <a href="/" class="font-bold">{{ config('app.name') }}</a>
        @auth
          <details class="relative">
            <summary class="truncate max-w-40">{{ Auth::user()->name ?? Auth::user()->email }}</summary>
            <div class="absolute top-full right-0 w-40 border p-2 bg-white mt-2 shadow">
              <ul>
                <li>
                  <a href="{{ rroute('profile.edit') }}" class="block px-3 py-2 underline">Profile</a>
                </li>
                <li>
                  <x-form method="POST" action="{{ rroute('logout') }}">
                    <button type="submit" class="block w-full text-left px-3 py-2 underline">Log out</button>
                  </x-form>
                </li>
              </ul>
            </div>
          </details>
        @endauth
      </div>
    </nav>
    <div class="container py-8 max-w-prose">
      {{ $slot }}
    </div>
  </body>
</html>
