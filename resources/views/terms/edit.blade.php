<x-layout title='Edit term'>
  <a href="{{ rroute('terms.show', ['term' => $term]) }}" class="underline inline-block mb-3">Back to term</a>
  <h1>Edit term</h1>

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

  <x-form method="PUT" action="{{ rroute('terms.update', ['term' => $term]) }}">
    <div>
      <label for="term">Term</label>
      <input type="text" id="term" name="term" value="{{ old('term') ?? $term->term }}" required />
    </div>

    <div>
      <label for="lang">Language</label>
      <select id="lang" name="lang" required>
        @foreach ($langs as $lang)
          <option
            value="{{ $lang->id }}"
            @if ($lang->id === (int) (old('lang') ?? $term->lang_id)) selected @endif
          >
            {{ $lang->name }}
          </option>
        @endforeach
      </select>
    </div>

    <fieldset>
      <legend>Definitions</legend>
      @php
        $emptyDef = ['definition' => '', 'examples' => [''], 'comment' => ''];
        $defs = $term->definitions->map(function ($def) {
          return [
            'definition' => $def->definition,
            'comment' => $def->comment,
            'examples' => $def->examples->map(function ($ex) {
              return $ex->example;
            }),
          ];
        });
      @endphp
      <ol
        x-data="{
          defs: {{ Js::from(old('defs') ?? $defs) }},
          newlyAddedThing: null, // could be 'definition' or 'example'
        }"
      >
        <template x-for="(def, i) in defs">
          <li>
            <div>
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
              <legend>Examples</legend>
              <ul>
                <template x-for="(_, j) in def.examples">
                  <li>
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
                    <button
                      type="button"
                      x-show="def.examples.length > 1"
                      x-on:click="def.examples.splice(j, 1)"
                    >
                      Remove this example
                    </button>
                    <button
                      type="button"
                      x-show="j === def.examples.length - 1 && def.examples.length < 3"
                      x-on:click="() => {
                        def.examples.push('');
                        newlyAddedThing = 'example';
                      }"
                    >
                      Add another example
                    </button>
                  </li>
                </template>
              </ul>
            </fieldset>
            <div>
              <label x-bind:for="`comment-${i}`">Comment</label>
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
              >
                Remove this definition
              </button>
            </template>
            <template x-if="i === defs.length - 1">
              <button
                type="button"
                x-on:click="() => {
                  defs.push({{ Js::from($emptyDef) }});
                  newlyAddedThing = 'definition';
                }"
              >
                Add another definition
              </button>
            </template>
          </li>
        </template>
      </ol>
    </fieldset>
    <button type="submit">Save term</button>
  </x-form>
</x-layout>
