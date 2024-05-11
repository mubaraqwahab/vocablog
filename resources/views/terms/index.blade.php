<x-layout title="My vocabulary">
  <h1 class="PageHeading">My vocabulary</h1>

  @if ($terms->count())
    <a href="{{ rroute('terms.create') }}" class="Button Button--primary mb-4">New term</a>

    <p class="text-sm text-gray-500 mb-4">
      Showing {{ $terms->count() }} out of {{ $terms->total() }} terms
    </p>

    <ul class="grid gap-4 mb-6">
      @foreach ($terms as $term)
        <li>
          <a href="{{ rroute('terms.show', ['term' => $term]) }}" class="flex items-baseline justify-between border rounded-md px-4 py-3 hover:bg-gray-50">
            <div class="flex items-baseline gap-4">
              <strong class="text-lg">{{ $term->term }}</strong>
              <span class="text-sm text-gray-500">{{ $term->lang->name }}</span>
            </div>
            <span class="text-sm text-gray-500">{{ $term->definitions_count }} {{ Str::plural('definition', $term->definitions_count) }}</span>
          </a>
        </li>
      @endforeach
    </ul>

    {{ $terms->links('partials.pagination') }}
  @else
    <p class="mb-3">You don't have any terms in your Vocablog.</p>
    <a href="{{ rroute('terms.create') }}" class="Button Button--primary">Add a new term</a>
  @endif
</x-layout>
