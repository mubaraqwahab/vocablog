<x-layout title="{{ $term->term }}">
  <h1 class="PageHeading">{{ $term->term }}</h1>
  <p class="text-sm uppercase mb-5 text-gray-500">{{ $term->lang->name }}</p>

  <ol class="pl-6 space-y-6">
    @foreach ($term->definitions as $def)
      <li class="list-decimal">
        <p>{{ $def->definition }}</p>
        @if (count($def->examples))
          <strong class="inline-block mt-2">Examples</strong>
          <ul class="pl-6 italic list-disc">
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

  <hr class="my-8">

  <div class="flex gap-3" x-data>
    <a href="{{ rroute('terms.edit', ['term' => $term, 'lang' => $term->lang]) }}" class="Button Button--secondary">Edit term</a>

    <button type="button" x-on:click="$refs.deleteTermDialog.showModal()" class="Button Button--danger">Delete term</button>
    <dialog
      x-ref="deleteTermDialog"
      class="border border-gray-300 rounded shadow-lg p-6 max-w-[calc(100%-4rem)] w-96 backdrop:bg-gray-200/60"
    >
      <h1 class="font-bold text-lg mb-2">Delete term</h1>
      <p class="mb-5">Are you sure you want to delete this term?</p>
      <x-form
        method="DELETE"
        action="{{ rroute('terms.destroy', ['term' => $term, 'lang' => $term->lang]) }}"
        class="flex gap-3 justify-end"
      >
        <button type="submit" formmethod="dialog" class="Button Button--secondary">No, keep it</button>
        <button type="submit" class="Button Button--danger">Yes, delete it</button>
      </x-form>
    </dialog>
  </div>
</x-layout>
