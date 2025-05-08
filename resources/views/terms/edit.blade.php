<x-layout title='Edit term'>
  <h1 class="PageHeading">Edit term</h1>

  @if ($errors->any())
    <x-banner variant="danger">
      <p>{{ $errors->count() }} {{ Str::plural('error', $errors->count()) }} occurred.</p>
    </x-banner>
  @endif

  <x-form method="PUT" action="{{ rroute('terms.update', ['term' => $term]) }}" class="flex flex-col gap-y-5">
    <div class="FormGroup">
      <label for="term" class="Label">
        <span class="Label-text">Term</span>
        <small class="Label-helper">A term can be a word, a phrase or any other form of expression that you learnt.</small>
        @foreach ($errors->get('term') as $error)
          <small class="Label-error">{{ $error }}</small>
        @endforeach
      </label>
      <input
        type="text"
        id="term"
        name="term"
        value="{{ old('term', $term->name) }}"
        required
        autocapitalize="none"
        autofocus
        class="FormControl"
        @error('term') aria-invalid="true" @enderror
      />
    </div>

    <div class="FormGroup">
      <label for="lang" class="Label">
        <span class="Label-text">Language</span>
        @foreach ($errors->get('lang') as $error)
          <small class="Label-error">{{ $error }}</small>
        @endforeach
      </label>
      <select
        id="lang"
        name="lang"
        required
        class="FormControl"
        @error('lang') aria-invalid="true" @enderror
      >
        @foreach ($langs as $lang)
          <option
            value="{{ $lang->id }}"
            @if ($lang->id === old('lang', $term->lang_id)) selected @endif
          >
            {{ $lang->name }}
          </option>
        @endforeach
      </select>
    </div>

    @php
      $normalizedDefs = collect(old('defs', $defs))->map(function ($def) {
          return [...$def, 'examples' => $def['examples'] ?? []];
      });
    @endphp

    <div
      class="mt-1"
      x-data="{
        defs: {{ Js::from($normalizedDefs) }},
        newlyAddedThing: null, // could be 'definition' or 'example'
      }"
    >
      <p class="mb-3">
        <strong class="text-lg">Definitions</strong>
      </p>

      <ol class="pl-6 list-decimal">
        <template x-for="(def, i) in defs" hidden>
          <li class="mb-5 space-y-5">
            <input type="hidden" x-bind:name="`defs[${i}][id]`" x-model="def.id" />

            <div class="FormGroup">
              <label x-bind:for="`def-${i}`" class="Label">
                <span class="Label-text">Definition</span>
              </label>
              <textarea
                x-bind:name="`defs[${i}][text]`"
                x-bind:id="`def-${i}`"
                x-model="def.text"
                x-init="() => {
                  if (newlyAddedThing === 'definition' && i === defs.length - 1) {
                    $nextTick(() => $el.focus());
                    newlyAddedThing = null;
                  }
                }"
                x-on:keydown.enter.prevent="$el.form.requestSubmit()"
                enterkeyhint="go"
                aria-multiline="false"
                required
                class="FormControl"
              ></textarea>
            </div>

            <div class="space-y-3">
              <p class="Label">
                <strong class="Label-text">Examples (optional)</strong>
                <span class="Label-helper">You can add up to 3 examples.</span>
              </p>

              <ul class="list-disc pl-6 space-y-3" x-show="def.examples.length > 0">
                <template x-for="(_, j) in def.examples" hidden>
                  <li class="space-y-2">
                    <div class="FormGroup">
                      <label x-bind:for="`example-${i}-${j}`" x-text="`Example ${j+1}`" class="Label Label-text"></label>
                      <textarea
                        x-bind:id="`example-${i}-${j}`"
                        x-bind:name="`defs[${i}][examples][${j}]`"
                        x-model="def.examples[j]"
                        x-init="() => {
                          if (newlyAddedThing === 'example' && j === def.examples.length - 1) {
                            $nextTick(() => $el.focus());
                            newlyAddedThing = null;
                          }
                        }"
                        x-on:keydown.enter.prevent="$el.form.requestSubmit()"
                        enterkeyhint="go"
                        aria-multiline="false"
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

            <div class="FormGroup">
              <label x-bind:for="`comment-${i}`" class="Label">
                <span class="Label-text">Comment (optional)</span>
                <small class="Label-helper">
                  Here you can write things like how to use this term,
                  or how this term compares to related terms.
                </small>
              </label>
              <textarea
                x-bind:name="`defs[${i}][comment]`"
                x-bind:id="`comment-${i}`"
                x-model="def.comment"
                x-on:keydown.enter.prevent="$el.form.requestSubmit()"
                enterkeyhint="go"
                aria-multiline="false"
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
