<x-layout title="My vocabulary">
  <div class="flex items-center justify-between mb-5">
    <h1 class="PageHeading mb-0">My vocabulary</h1>
    <!-- TODO: Add a quiz btn somewhere -->
    @if ($allTermsCount > 0)
      <div class="flex items-center">
        <a href="{{ rroute('terms.create') }}" class="Button Button--primary">Add term</a>
      </div>
    @endif
  </div>

  @if (session('status') === 'term-deleted')
    <x-banner variant="success" dismissable class="mb-5">
      <p>Term deleted</p>
    </x-banner>
  @endif

  @if ($allTermsCount > 0)
    <x-form class="flex items-center flex-wrap gap-4 mb-5 pb-6">
      <div class="FormGroup FormGroup--horizontal items-center flex-grow">
        <label class="Label Label-text text-sm" for="term">Term</label>
        <input name="term" id="term" class="FormControl text-sm w-full bg-gray-50/80" value="{{ request()->query('term') }}" />
      </div>
      <div class="FormGroup FormGroup--horizontal items-center flex-grow sm:flex-grow-0">
        <label for="lang" class="Label Label-text text-sm">Language</label>
        <select id="lang" name="lang" class="FormControl w-full sm:w-auto bg-gray-50/80 text-sm">
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

    @if ($terms->count() > 0)
      <p class="flex flex-col gap-y-1 sm:block text-sm text-gray-500 mb-4">
        <span>Showing {{ $terms->firstItem() }} to {{ $terms->lastItem() }} of {{ $terms->total() }} terms</span>
        @if ($terms->total() >= config('app.min_terms_count_for_quiz'))
          <span class="hidden sm:inline">&middot;</span>
          <a href="{{ rroute('quiz') }}" class="underline">Want to test your knowledge?</a>
        @endif
      </p>

      <ul class="flex flex-col gap-4 mb-6">
        @foreach ($terms as $term)
          <li>
            <a
              href="{{ rroute('terms.show', ['term' => $term]) }}"
              class="flex flex-col gap-2 sm:gap-4 sm:flex-row sm:justify-between sm:items-baseline border rounded-md px-4 py-3 motion-safe:transition-colors hover:bg-gray-50"
            >
              <strong class="text-lg truncate">{{ $term->name }}</strong>
              <span class="text-sm text-gray-500 flex-shrink-0 flex gap-x-2">
                <span>{{ $term->lang->name }}</span>
                <span>&middot;</span>
                <span>{{ $term->definitions_count }} {{ Str::plural('definition', $term->definitions_count) }}</span>
              </span>
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
