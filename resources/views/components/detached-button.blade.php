@props(['formmethod' => 'GET'])

@php($uuid = Str::uuid())

<button form="detached-form-{{ $uuid }}" {{ $attributes }}>{{ $slot }}</button>
<x-form id="detached-form-{{ $uuid }}" :method="$formmethod" class="hidden"></x-form>
