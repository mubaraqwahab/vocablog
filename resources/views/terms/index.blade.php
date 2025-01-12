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

  @if ($allTermsCount > 0)
    <x-form class="flex items-center flex-wrap gap-4 mb-4">
      <div class="FormGroup FormGroup--horizontal items-center">
        <label class="Label Label-text text-sm" for="term">Term</label>
        <input name="term" id="term" class="FormControl text-sm" value="{{ request()->query('term') }}" />
      </div>
      <div class="FormGroup FormGroup--horizontal items-center">
        <label for="lang" class="Label Label-text text-sm">Language</label>
        <select id="lang" name="lang" class="FormControl text-sm">
          <option value="">All</option>
          @foreach ($langs as $lang)
            <option
              value="{{ $lang->id }}"
              @selected($lang->id === request()->query('lang'))
            >{{ $lang->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="Button Button--secondary">Search</button>
    </x-form>

    <div class="flex items-center">
      <a href="{{ rroute('terms.create') }}" class="Button Button--primary mb-4">New term</a>
    </div>

    @if ($terms->count() > 0)
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
      <p class="mb-3 mt-4">You don't have any terms matching your search.</p>
    @endif
  @else
    <p class="mb-3">You don't have any terms in your {{ config('app.name') }}.</p>
    <a href="{{ rroute('terms.create') }}" class="Button Button--primary">Add a new term</a>
  @endif
</x-layout>
