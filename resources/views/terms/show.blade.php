<x-layout title="{{ $term->term }}">
  <a href="{{ rroute('terms.index') }}" class="underline inline-block mb-3">Back to terms</a>
  <h1 class="PageHeading">{{ $term->term }}</h1>
  <p class="text-sm uppercase mb-4">{{ $term->lang->name }}</p>

  {{-- <h2 class="font-bold text-gray-900 text-xl">Definitions</h2> --}}
  <ol class="ml-8">
    @foreach ($term->definitions as $def)
      <li class="py-3 list-decimal">
        <p>{{ $def->definition }}</p>
        <strong class="inline-block mt-2">Examples</strong>
        <ul class="ml-8 italic list-disc">
          @foreach ($def->examples as $e)
            <li>{{ $e->example }}</li>
          @endforeach
        </ul>
        @if ($def->comment)
          <strong class="inline-block mt-2">Comment</strong>
          <p>{{ $def->comment }}</p>
        @endif
      </li>
    @endforeach
  </ol>

  <a href="{{ rroute('terms.edit', ['term' => $term]) }}" class="underline">Edit term</a>
  <div x-data>
    <button type="button" x-on:click="$refs.deleteTermDialog.showModal()" class="underline">Delete term</button>
    <dialog x-ref="deleteTermDialog" class="border shadow-sm rounded-md p-4">
      <strong class="">Delete term</strong>
      <p>Are you sure you want to delete this term?</p>
      <x-form method="DELETE" action="{{ rroute('terms.destroy', ['term' => $term]) }}">
        <button type="submit" formmethod="dialog" class="Button">No, keep it</button>
        <button type="submit" class="Button">Yes, delete it</button>
      </x-form>
    </dialog>
  </div>
</x-layout>
