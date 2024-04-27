<x-layout title="New term">
  <h1>New term</h1>

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
    <div>
      <label for="term">Term</label>
      <input type="text" id="term" name="term" value="{{ old('term') }}" required />
    </div>

    <div>
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
      <legend>Definitions</legend>
      @php
        $emptyDef = ['definition' => '', 'examples' => [''], 'comment' => ''];
      @endphp
      <ol
        x-data="{
          defs: {{ Js::from(old('defs') ?? [$emptyDef]) }},
          newlyAdded: null, // could be 'definition' or 'example'
        }"
      >
        <template x-for="(def, i) in defs">
          <li>
            <fieldset>
              <div>
                <label :for="`definition-${i}`">Definition</label>
                <textarea
                  :name="`defs[${i}][definition]`"
                  :id="`definition-${i}`"
                  x-model="def.definition"
                  x-effect="(() => {
                    if (newlyAdded === 'definition' && i === defs.length - 1) {
                      $el.focus();
                      newlyAdded = null;
                    }
                  })()"
                  required
                ></textarea>
              </div>
              <fieldset>
                <legend>Examples</legend>
                <ul>
                  <template x-for="(_, j) in def.examples">
                    <li>
                      <label :for="`example-${i}-${j}`" x-text="`Example ${j+1}`"></label>
                      <input
                        type="text"
                        :id="`example-${i}-${j}`"
                        :name="`defs[${i}][examples][${j}]`"
                        x-model="def.examples[j]"
                        x-effect="(() => {
                          if (newlyAdded === 'example' && j === def.examples.length - 1) {
                            $el.focus();
                            newlyAdded = null;
                          }
                        })()"
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
                        x-show="j === def.examples.length - 1"
                        x-on:click="(() => {
                          def.examples.push('');
                          newlyAdded = 'example';
                        })()"
                      >
                        Add another example
                      </button>
                    </li>
                  </template>
                </ul>
              </fieldset>
              <div>
                <label :for="`comment-${i}`">Comment</label>
                <textarea
                  :name="`defs[${i}][comment]`"
                  :id="`comment-${i}`"
                  x-model="def.comment"
                ></textarea>
              </div>
            </fieldset>
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
              x-on:click="(() => {
                defs.push({{ Js::from($emptyDef) }});
                newlyAdded = 'definition';
              })"
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
