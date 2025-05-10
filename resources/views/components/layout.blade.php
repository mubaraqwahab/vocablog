<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' - ' . config('app.name') : config('app.name') . ' - ' . config('app.tagline') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="text-gray-700 bg-white">
    <nav class="border-b bg-gray-50">
      <div class="flex justify-between items-center container py-3 max-w-prose relative">
        <a href="#main" class="[&:not(:focus)]:sr-only absolute bg-white border px-2 py-1 rounded inline-block">Skip to main content</a>
        <a href="/" class="font-bold">{{ config('app.name') }}</a>
        @auth
          <details class="relative group">
            <summary class="inline-flex items-center gap-x-2 truncate max-w-40 px-2 py-1 -mr-2 rounded motion-safe:transition-colors hover:bg-gray-200">
              <span>{{ Auth::user()->name ?? Auth::user()->email }}</span>
              <i data-lucide="chevron-down" class="h-4 w-4 group-open:rotate-180 motion-safe:transition-transform"></i>
            </summary>
            <details-menu role="menu" class="absolute top-full right-0 w-40 border p-1 bg-white mt-2 rounded shadow">
              <a
                href="{{ rroute('settings.edit') }}"
                role="menuitem"
                class="block w-full text-left px-3 py-2 rounded motion-safe:transition-colors hover:bg-gray-100"
              >
                Settings
              </a>
              <x-detached-button
                type="submit"
                role="menuitem"
                class="block w-full text-left px-3 py-2 rounded motion-safe:transition-colors hover:bg-gray-100"
                formmethod="POST"
                formaction="{{ rroute('logout') }}"
              >
                Log out
              </x-detached-button>
            </details-menu>
          </details>
        @endauth
      </div>
    </nav>
    <main class="container py-8 sm:py-10 max-w-prose" id="main">
      {{ $slot }}
    </main>
  </body>
</html>
