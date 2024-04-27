@props(['method' => 'GET'])

@php
  $upperMethod = strtoupper($method);
  if ($upperMethod === 'GET' || $upperMethod === 'POST') {
    $htmlMethod = $upperMethod;
  } else {
    $htmlMethod = 'POST';
  }
@endphp

<form method="{{ $htmlMethod }}" {{ $attributes }}>
  @if ($upperMethod !== 'GET') @csrf @endif
  @if ($upperMethod !== $htmlMethod) @method($upperMethod) @endif
  {{ $slot }}
</form>
