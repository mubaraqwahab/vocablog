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
<i>{{ $definition->comment }}</i>
@endif
@endforeach

</x-mail::panel>
@endforeach

@if ($user->all_terms_count >= config('app.min_terms_count_for_quiz'))
Want to test your knowledge so far? [Take a quiz!]({{ route('quiz') }})
@endif

</x-mail::message>
