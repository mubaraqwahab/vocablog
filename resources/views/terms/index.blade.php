<x-layout title="My vocabulary">
  <div class="container">
    <h1 class="text-3xl font-bold">My vocabulary</h1>
    <a href="{{ route('terms.create') }}">New term</a>

    @if (count($terms))
      <p>Showing {{ count($terms) }} out of {{ $terms->total() }} terms.</p>
      <ul>
        @foreach ($terms as $term)
          <li>
            <a href="{{ route('terms.show', ['term' => $term]) }}">
              <strong>{{ $term->term }}</strong>
              <span>{{ $term->lang->name }}</span>
              <span>{{ $term->definitions_count }} {{ Str::plural('definition', $term->definitions_count) }}</span>
            </a>
          </li>
        @endforeach
      </ul>
    @else
      <p>No terms</p>
    @endif
  </div>
</x-layout>
