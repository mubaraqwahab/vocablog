<x-layout title="New term">
  <h1>New term</h1>
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  <form method="POST" action="{{ route("terms.store") }}">
    @csrf
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
            @if (old('lang') === $lang->id) selected @endif
          >
            {{ $lang->name }}
          </option>
        @endforeach
      </select>
    </div>
    <div>
      <fieldset>
        <legend>Definitions</legend>
        @php
          $emptyDef = ['definition' => '', 'examples' => [''], 'comment' => ''];
        @endphp
        <ul x-data="{ defs: {{ Js::from(old('defs') ?? [$emptyDef]) }}, addedNewExample: false }">
          <template x-for="(def, i) in defs">
            <li>
              <fieldset>
                <div>
                  <label :for="`definition-${i}`">Definition</label>
                  <textarea
                    x-bind:name="`defs[${i}][definition]`"
                    x-bind:value="def.definition"
                    :id="`definition-${i}`"
                    required
                  ></textarea>
                </div>
                <fieldset>
                  <legend>Examples</legend>
                  <ul>
                    <template x-for="(example, j) in def.examples">
                      <li>
                        <label :for="`example-${i}-${j}`">Example 1</label>
                        <input
                          type="text"
                          :id="`example-${i}-${j}`"
                          x-bind:name="`defs[${i}][examples][${j}]`"
                          x-bind:value="example"
                          x-effect="(() => {
                            if (addedNewExample && j === def.examples.length - 1) {
                              $el.focus();
                              addedNewExample = false;
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
                          x-on:click="(() => {
                            def.examples.push('');
                            addedNewExample = true;
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
                    x-bind:name="`defs[${i}][comment]`"
                    x-bind:value="def.comment"
                    :id="`comment-${i}`"
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
                x-on:click="defs.push({{ Js::from($emptyDef) }})"
              >
                Add another definition
              </button>
            </li>
          </template>
        </ul>
      </fieldset>
    </div>
    <button type="submit">Save term</button>
  </form>
</x-layout>
