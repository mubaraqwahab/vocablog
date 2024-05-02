<x-layout title="New term">
  <h1 class="PageHeading">New term</h1>

  @if ($errors->any())
    <div>
      <strong>Errors</strong>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <x-form method="POST" action="{{ route('terms.store') }}">
    <div class="flex flex-col gap-1 mb-5">
      <label for="term">Term</label>
      <input type="text" id="term" name="term" value="{{ old('term') }}" required />
    </div>

    <div class="flex flex-col gap-1 mb-5">
      <label for="lang">Language</label>
      <select id="lang" name="lang" required>
        @foreach ($langs as $lang)
          <option
            value="{{ $lang->id }}"
            @if ($lang->id === (int) old('lang')) selected @endif
          >
            {{ $lang->name }}
          </option>
        @endforeach
      </select>
    </div>

    <fieldset>
      <legend class="font-bold text-lg mb-3">Definitions</legend>
      @php
        $emptyDef = ['definition' => '', 'examples' => [''], 'comment' => ''];
      @endphp
      <ol
        class="ml-4"
        x-data="{
          defs: {{ Js::from(old('defs') ?? [$emptyDef]) }},
          newlyAddedThing: null, // could be 'definition' or 'example'
        }"
      >
        <template x-for="(def, i) in defs">
          <li>
            <div class="flex flex-col gap-1 mb-5">
              <label x-bind:for="`definition-${i}`">Definition</label>
              <textarea
                x-bind:name="`defs[${i}][definition]`"
                x-bind:id="`definition-${i}`"
                x-model="def.definition"
                x-on:keydown.enter.prevent
                x-init="() => {
                  if (newlyAddedThing === 'definition' && i === defs.length - 1) {
                    $el.focus();
                    newlyAddedThing = null;
                  }
                }"
                required
              ></textarea>
            </div>
            <fieldset>
              <legend class="font-bold mb-3">Examples</legend>
              <ul class="ml-4">
                <template x-for="(_, j) in def.examples">
                  <li class="grid grid-cols-[1fr_auto_auto] gap-1">
                    <div class="flex flex-col gap-1 mb-5">
                      <label x-bind:for="`example-${i}-${j}`" x-text="`Example ${j+1}`"></label>
                      <input
                        type="text"
                        x-bind:id="`example-${i}-${j}`"
                        x-bind:name="`defs[${i}][examples][${j}]`"
                        x-model="def.examples[j]"
                        x-init="() => {
                          if (newlyAddedThing === 'example' && j === def.examples.length - 1) {
                            $el.focus();
                            newlyAddedThing = null;
                          }
                        }"
                        required
                      />
                    </div>
                    <button
                      type="button"
                      x-show="def.examples.length > 1"
                      x-on:click="def.examples.splice(j, 1)"
                    >
                      Remove this example
                    </button>
                  </li>
                </template>
              </ul>
              <button
                type="button"
                x-on:click="() => {
                  def.examples.push('');
                  newlyAddedThing = 'example';
                }"
              >
                Add another example
              </button>
            </fieldset>
            <div class="flex flex-col gap-1 mb-5">
              <label x-bind:for="`comment-${i}`">Comment</label>
              <textarea
                x-bind:name="`defs[${i}][comment]`"
                x-bind:id="`comment-${i}`"
                x-model="def.comment"
                x-on:keydown.enter.prevent
              ></textarea>
            </div>
            <button
              type="button"
              x-show="defs.length > 1"
              x-on:click="defs.splice(i, 1)"
            >
              Remove this definition
            </button>
            <button
              type="button"
              x-show="i === defs.length - 1"
              x-on:click="
                defs.push({{ Js::from($emptyDef) }});
                newlyAddedThing = 'definition';
              "
            >
              Add another definition
            </button>
          </li>
        </template>
      </ol>
    </fieldset>
    <button type="submit">Save term</button>
  </x-form>
</x-layout>
