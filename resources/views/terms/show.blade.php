<x-layout title="{{ $term->term }}">
  <a href="{{ route('terms.index') }}">Back to terms</a>
  <h1>{{ $term->term }}</h1>
  <p>{{ $term->lang->name }}</p>

  <h2>Definitions</h2>
  <ol>
    @foreach ($term->definitions as $def)
      <li>
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
      </li>
    @endforeach
  </ol>

  <a href="{{ route('terms.edit', ['term' => $term]) }}">Edit term</a>
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
