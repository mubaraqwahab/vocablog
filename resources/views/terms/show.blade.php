<x-layout title="{{ $term->term }}">
  <h1>{{ $term->term }}</h1>
  <p>{{ $term->lang->name }}</p>
  <a href="{{ route('terms.edit', ['term' => $term]) }}">Edit term</a>
  <button type="button">Delete term</button>
  <h2>Definitions</h2>
  <ol>
    @foreach ($term->definitions as $def)
      <li>
        <p>{{ $def->definition }}</p>
        <strong>Examples</strong>
        <ul>
          @foreach ($def->examples as $example)
          <li>{{ $example->example }}</li>
          @endforeach
        </ul>
        @if ($def->comment)
          <strong>Comment</strong>
          <p>{{ $def->comment }}</p>
        @endif
      </li>
    @endforeach
  </ol>
</x-layout>
