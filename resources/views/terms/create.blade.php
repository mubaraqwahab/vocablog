<x-layout title="New term">
  <a href="{{ rroute('terms.index') }}" class="underline inline-block mb-3">Back to terms</a>
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

  <x-form method="POST" action="{{ rroute('terms.store') }}">
    <div class="flex flex-col gap-1 mb-5 md:w-72">
      <label for="term">Term</label>
      <input type="text" id="term" name="term" value="{{ old('term') }}" required />
    </div>

    <div class="flex flex-col gap-1 mb-5 md:w-72">
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

    <fieldset class="mb-3">
      <legend class="font-bold text-lg mb-3">Definitions</legend>
      @php
        $emptyDef = ['definition' => '', 'examples' => [], 'comment' => ''];
      @endphp
      <ol
        class="ml-4 list-decimal"
        x-data="{
          defs: {{ Js::from(old('defs') ?? [$emptyDef]) }},
          newlyAddedThing: null, // could be 'definition' or 'example'
        }"
      >
        <template x-for="(def, i) in defs">
          <li class="mb-5">
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
              <legend class="mb-3">
                <strong class="font-bold">Examples</strong>
                <p>You can add up to 3 examples.</p>
              </legend>

              <ul class="list-disc pl-4">
                <template x-for="(_, j) in def.examples">
                  <li>
                    <div class="grid grid-cols-[1fr_auto_auto] gap-1">
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
                          x-on:click="def.examples.splice(j, 1)"
                          class="Button self-center"
                        >
                          Delete example
                        </button>
                    </div>
                  </li>
                </template>
              </ul>

              <template x-if="def.examples.length < 3">
                <button
                  type="button"
                  x-on:click="() => {
                    def.examples.push('');
                    newlyAddedThing = 'example';
                  }"
                  x-text="def.examples.length === 0 ? 'Add an example' : 'Add another example'"
                  class="Button mb-5"
                ></button>
              </template>
            </fieldset>

            <div class="flex flex-col gap-1 mb-5">
              <label x-bind:for="`comment-${i}`">Comment (optional)</label>
              <textarea
                x-bind:name="`defs[${i}][comment]`"
                x-bind:id="`comment-${i}`"
                x-model="def.comment"
                x-on:keydown.enter.prevent
              ></textarea>
            </div>

            <template x-if="defs.length > 1">
              <button
                type="button"
                x-on:click="defs.splice(i, 1)"
                class="Button"
              >
                Delete definition
              </button>
            </template>
          </li>
        </template>
      </ol>
      <button
        type="button"
        x-on:click="() => {
          defs.push({{ Js::from($emptyDef) }});
          newlyAddedThing = 'definition';
        }"
        class="Button"
      >
        Add another definition
      </button>
    </fieldset>

    <button type="submit" class="Button">Save term</button>
  </x-form>
</x-layout>
