<x-layout title="My vocabulary">
  <div class="container my-8">
    <h1 class="text-3xl font-bold mb-4">My vocabulary</h1>

    @if (count($terms))
      <a href="{{ route('terms.create') }}" class="px-3 py-1.5 border rounded-md inline-block font-bold mb-4">New term</a>
      <p class="text-sm text-gray-500 mb-4">Showing {{ count($terms) }} out of {{ $terms->total() }} terms</p>
      <ul class="grid gap-4">
        @foreach ($terms as $term)
          <li>
            <a href="{{ route('terms.show', ['term' => $term]) }}" class="flex items-baseline justify-between border rounded-md px-4 py-3 hover:bg-gray-50">
              <div class="flex items-baseline gap-4">
                <strong class="text-lg">{{ $term->term }}</strong>
                <span class="text-sm text-gray-500">{{ $term->lang->name }}</span>
              </div>
              <span class="text-sm text-gray-500">{{ $term->definitions_count }} {{ Str::plural('definition', $term->definitions_count) }}</span>
            </a>
          </li>
        @endforeach
      </ul>
    @else
      <p>You don't have any terms in your Vocablog. <a href="{{ route('terms.create') }}">Add a new term</a></p>
    @endif
  </div>
</x-layout>
