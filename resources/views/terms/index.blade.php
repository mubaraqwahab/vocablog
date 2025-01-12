<x-layout title="My vocabulary">
  <h1 class="PageHeading">My vocabulary</h1>

  @if (session('status') === 'term-deleted')
    <div
      x-data="{ open: true }" x-show="open"
      class="flex justify-between items-center mb-4 border border-green-300 bg-green-100 text-green-700 rounded px-4 py-2"
    >
      <p>Term deleted</p>
      <button type="button" x-on:click="open = false;">Dismiss</button>
    </div>
  @endif

  <!-- TODO: How should search work when the user has no terms at all? -->

  @if ($terms->count())
    <form>
      <label class="sr-only" for="term">Search</label>
      <input name="term" id="term" />
      <label for="lang">Language</label>
      <select id="lang" name="lang">
        <option>English</option>
        <option>Yoruba</option>
      </select>
      <button type="submit" class="Button Button--secondary">Search</button>
    </form>
    <div class="flex items-center">
      <a href="{{ rroute('terms.create') }}" class="Button Button--primary mb-4">New term</a>
    </div>

    <p class="text-sm text-gray-500 mb-4">
      Showing {{ $terms->firstItem() }} to {{ $terms->lastItem() }} of {{ $terms->total() }} terms
    </p>

    <ul class="grid gap-4 mb-6">
      @foreach ($terms as $term)
        <li>
          <a href="{{ rroute('terms.show', ['term' => $term]) }}" class="flex items-baseline justify-between border rounded-md px-4 py-3 hover:bg-gray-50">
            <div class="flex items-baseline gap-4">
              <strong class="text-lg">{{ $term->name }}</strong>
              <span class="text-sm text-gray-500">{{ $term->lang->name }}</span>
            </div>
            <span class="text-sm text-gray-500">{{ $term->definitions_count }} {{ Str::plural('definition', $term->definitions_count) }}</span>
          </a>
        </li>
      @endforeach
    </ul>

    {{ $terms->links('partials.pagination') }}
  @else
    <p class="mb-3">You don't have any terms in your {{ config('app.name') }}.</p>
    <a href="{{ rroute('terms.create') }}" class="Button Button--primary">Add a new term</a>
  @endif
</x-layout>
