<x-mail::message>
# Weekly Digest

Hello {{ $user->name ?? $user->email }}, it's revision time!

You learnt these new terms in the past week:

@foreach ($user->terms as $term)
<x-mail::panel>

<strong>{{ $term->name }}</strong>

@foreach ($term->definitions as $definition)
{{ $definition->text }}

@foreach ($definition->examples as $example)
- <i>{{ $example }}</i>
@endforeach

@if ($definition->comment)
{{ $definition->comment }}
@endif
@endforeach

</x-mail::panel>
@endforeach

</x-mail::message>
