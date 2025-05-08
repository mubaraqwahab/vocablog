@props(['variant', 'dismissable' => false])

@php
  if ($attributes->get('x-show')) {
    throw new \Exception("Do not use Alpine's x-show attribute directly on a banner component.");
  }

  $class = [
    'flex justify-between items-center border rounded px-4 py-2 font-medium',
    'border-green-300 bg-green-100 text-green-700' => $variant === 'success',
    'border-red-300 bg-red-100 text-red-700' => $variant === 'danger',
  ];
@endphp

<div
  x-data="{ $bannerOpen: true }"
  x-show="$bannerOpen"
  {{ $attributes->class($class) }}
>
  {{ $slot }}
  @if ($dismissable)
    <button type="button" x-on:click="$bannerOpen = false;" class="p-1 -m-1 rounded hover:bg-black/5" aria-label="Dismiss">
      <i data-lucide="x" class="h-4 w-4"></i>
    </button>
  @endif
</div>
