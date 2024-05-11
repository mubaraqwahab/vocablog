<x-layout title="{{ $term->term }}">
  <h1 class="PageHeading">{{ $term->term }}</h1>
  <p class="text-sm uppercase mb-4">{{ $term->lang->name }}</p>

  {{-- <h2 class="font-bold text-gray-900 text-xl">Definitions</h2> --}}
  <ol class="ml-8 mb-8">
    @foreach ($term->definitions as $def)
      <li class="py-3 list-decimal">
        <p>{{ $def->definition }}</p>
        @if (count($def->examples))
          <strong class="inline-block mt-2">Examples</strong>
          <ul class="ml-8 italic list-disc">
            @foreach ($def->examples as $e)
              <li>{{ $e->example }}</li>
            @endforeach
          </ul>
        @endif
        @if ($def->comment)
          <strong class="inline-block mt-2">Comment</strong>
          <p>{{ $def->comment }}</p>
        @endif
      </li>
    @endforeach
  </ol>

  <div class="flex gap-3" x-data>
    <a href="{{ rroute('terms.edit', ['term' => $term]) }}" class="Button Button--secondary">Edit term</a>

    <button type="button" x-on:click="$refs.deleteTermDialog.showModal()" class="Button Button--danger">Delete term</button>
    <dialog x-ref="deleteTermDialog" class="border shadow-sm p-6">
      <strong class="font-bold mb-3">Delete term</strong>
      <p class="mb-3">Are you sure you want to delete this term?</p>
      <x-form method="DELETE" action="{{ rroute('terms.destroy', ['term' => $term]) }}" class="flex gap-3 justify-end">
        <button type="submit" formmethod="dialog" class="Button Button--secondary">No, keep it</button>
        <button type="submit" class="Button Button--danger">Yes, delete it</button>
      </x-form>
    </dialog>
  </div>
</x-layout>
