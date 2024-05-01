<x-layout title="{{ $term->term }}">
  <a href="{{ route('terms.index') }}">Back to terms</a>
  <h1>{{ $term->term }}</h1>
  <p>{{ $term->lang->name }}</p>

  <h2>Definitions</h2>
  <ol>
    @foreach ($term->definitions as $def)
      @php
        $id = $def->id;
        $defData = [
          'id' => $def->id,
          'definition' => $def->definition,
          'comment' => $def->comment,
          'examples' => $def->examples->map(fn ($e) => $e->example)->toArray(),
        ];
      @endphp

      <li x-data>
        <p>{{ $def->definition }}</p>
        <strong>Examples</strong>
        <ul>
          @foreach ($def->examples as $e)
            <li>{{ $e->example }}</li>
          @endforeach
        </ul>
        @if ($def->comment)
          <strong>Comment</strong>
          <p>{{ $def->comment }}</p>
        @endif

        <button
          type="button"
          x-on:click="document.getElementById('editDefDialog-{{ $id }}').showModal()"
        >
          Edit definition
        </button>
        <x-def-dialog id="editDefDialog-{{ $id }}" :termId="$term->id" :$defData></x-def-dialog>

        <button
          type="button"
          x-on:click="document.getElementById('deleteDefDialog-{{ $id }}').showModal()"
        >
          Delete definition
        </button>
        <dialog id="deleteDefDialog-{{ $id }}">
          <p>Are you sure you want to delete this definition?</p>
          <x-form method="DELETE" action="{{ route('definitions.destroy', ['definition' => $id]) }}">
            <button type="submit" formmethod="dialog">No, keep it</button>
            <button type="submit">Yes, delete it</button>
          </x-form>
        </dialog>
      </li>
    @endforeach
  </ol>

  <div x-data>
    @php
      $defData = [
        'id' => 0,
        'definition' => '',
        'comment' => '',
        'examples' => [''],
      ];
    @endphp

    <button
      type="button"
      x-on:click="document.getElementById('createDefDialog').showModal()"
    >
      Add definition
    </button>
    <x-def-dialog id="createDefDialog" :termId="$term->id" :$defData></x-def-dialog>
  </div>

  <div x-data>
    <button type="button" x-on:click="$refs.deleteTermDialog.showModal()">Delete term</button>
    <dialog x-ref="deleteTermDialog">
      <p>Are you sure you want to delete this term?</p>
      <x-form method="DELETE" action="{{ route('terms.destroy', ['term' => $term], absolute: false) }}">
        <button type="submit" formmethod="dialog">No, keep it</button>
        <button type="submit">Yes, delete it</button>
      </x-form>
    </dialog>
  </div>

</x-layout>
