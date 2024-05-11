<x-layout title="New term">
  <h1 class="PageHeading">Add a new term</h1>

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

  <x-form method="POST" action="{{ rroute('terms.store') }}" class="flex flex-col gap-y-5">
    <p class="italic text-gray-500">Required fields are marked with an asterisk (*).</p>

    <div class="FormGroup">
      <label for="term" class="Label">
        <span class="Label-text">Term *</span>
        <small class="Label-helper">A term can be a word, a phrase or any other form of expression.</small>
      </label>
      <input
        type="text" id="term" name="term" value="{{ old('term') }}"
        required autocapitalize="off"
        class="FormControl"
      />
    </div>

    <div class="FormGroup">
      <label for="lang" class="Label Label-text">Language *</label>
      <select id="lang" name="lang" required class="FormControl">
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

    @php
      $emptyDef = ['definition' => '', 'examples' => [], 'comment' => ''];
    @endphp

    <div
      class="mt-1"
      x-data="{
        defs: {{ Js::from(old('defs') ?? [$emptyDef]) }},
        newlyAddedThing: null, // could be 'definition' or 'example'
      }"
    >
      <p class="mb-3">
        <strong class="text-lg">Definitions</strong>
      </p>

      <ol class="pl-6 list-decimal">
        <template x-for="(def, i) in defs" hidden>
          <li class="mb-5 space-y-5">
            <div class="FormGroup">
              <label x-bind:for="`definition-${i}`" class="Label Label-text">Definition *</label>
              <textarea
                x-bind:name="`defs[${i}][definition]`"
                x-bind:id="`definition-${i}`"
                x-model="def.definition"
                x-on:keydown.enter.prevent
                x-init="() => {
                  if (newlyAddedThing === 'definition' && i === defs.length - 1) {
                    $nextTick(() => $el.focus());
                    newlyAddedThing = null;
                  }
                }"
                required
                class="FormControl"
              ></textarea>
            </div>

            <div class="space-y-3">
              <p class="flex flex-col">
                <strong>Examples</strong>
                <span class="text-gray-500">You can add up to 3 examples.</span>
              </p>

              <ul class="list-disc pl-6 space-y-3" x-show="def.examples.length > 0">
                <template x-for="(_, j) in def.examples" hidden>
                  <li class="space-y-2">
                    <div class="FormGroup">
                      <label x-bind:for="`example-${i}-${j}`" x-text="`Example ${j+1} *`" class="Label Label-text"></label>
                      <textarea
                        x-bind:id="`example-${i}-${j}`"
                        x-bind:name="`defs[${i}][examples][${j}]`"
                        x-model="def.examples[j]"
                        x-on:keydown.enter.prevent
                        x-init="() => {
                          if (newlyAddedThing === 'example' && j === def.examples.length - 1) {
                            console.log('inited textarea', { newlyAddedThing, j, l: def.examples.length, $el })
                            $nextTick(() => $el.focus());
                            newlyAddedThing = null;
                          }
                        }"
                        required
                        class="FormControl"
                      ></textarea>
                    </div>

                    <button
                      type="button"
                      x-on:click="def.examples.splice(j, 1)"
                      class="Button Button--danger"
                    >
                      Delete example
                    </button>
                  </li>
                </template>
              </ul>

              <template x-if="def.examples.length < 3" hidden>
                <button
                  type="button"
                  x-on:click="() => {
                    def.examples.push('');
                    newlyAddedThing = 'example';
                  }"
                  x-text="def.examples.length === 0 ? 'Add an example' : 'Add another example'"
                  class="Button Button--secondary"
                ></button>
              </template>
            </div>

            <div class="flex flex-col gap-1.5 mb-5">
              <label x-bind:for="`comment-${i}`" class="Label">
                <span class="Label-text">Comment</span>
                <small class="Label-helper">
                  Here you can write things like how to use this term,
                  or how this term compares to related terms.
                </small>
              </label>
              <textarea
                x-bind:name="`defs[${i}][comment]`"
                x-bind:id="`comment-${i}`"
                x-model="def.comment"
                x-on:keydown.enter.prevent
                class="FormControl"
              ></textarea>
            </div>

            <template x-if="defs.length > 1" hidden>
              <button
                type="button"
                x-on:click="defs.splice(i, 1)"
                class="Button Button--danger"
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
        class="Button Button--secondary"
      >
        Add another definition
      </button>
    </div>

    <button type="submit" class="Button Button--primary self-start">Save term</button>
  </x-form>
</x-layout>
