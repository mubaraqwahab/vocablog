<x-mail::message>
# Weekly Digest

Hello {{ $user->name ?? $user->email }}, it's revision time!

You learnt these new terms in the past week:

<ol>
@foreach ($user->terms as $term)
<li>
<strong>{{ $term->name }}</strong><br>
@foreach ($term->definitions as $definition)
{{ $definition->text }}

@foreach ($definition->examples as $example)
  - <i>{{ $example }}</i>
@endforeach

@if ($definition->comment)
**Comment**: {{ $definition->comment }}
@endif
<br>
@endforeach
</li>
@endforeach
</ol>

</x-mail::message>
