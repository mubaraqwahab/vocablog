<x-layout title="New term">
  <h1>New term</h1>
  <form method="POST" action="{{ route("terms.store") }}">
    @csrf
    <div>
      <label for="term">Term</label>
      <input type="text" id="term" name="term" required />
    </div>
    <div>
      <label for="lang">Language</label>
      <select id="lang" name="lang" required>
        @foreach ($langs as $lang)
          <option value="{{ $lang->id }}">{{ $lang->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <fieldset>
        <legend>Definitions</legend>
        <ul x-data="{ definitions: [{ definition: '', examples: [''],  }] }">
          <template x-for="(def, i) in definitions">
            <li>
              <fieldset>
                <legend>First def</legend>
                <div>
                  <label for="definition">Definition</label>
                  <textarea x-bind:name="`definition[${i}]`" x-bind:value="def.definition" id="definition" required></textarea>
                </div>
                <fieldset>
                  <legend>Examples</legend>
                  <ul>
                    <template x-for="(example, j) in def.examples">
                      <div>
                        <label for="example1">Example 1</label>
                        <input type="text" name="example1" id="example1" x-bind:name="`example[${j}]`" x-bind:value="def.example" required />
                        <button type="button" x-show="def.examples.length > 1" x-on:click="def.examples.splice(j, 1)">Remove this example</button>
                        <button type="button" x-on:click="def.examples.push('')">Add another example</button>
                      </div>
                    </template>
                  </ul>
                </fieldset>
                <div>
                  <label for="comment">Comment</label>
                  <textarea name="comment" id="comment" required></textarea>
                </div>
              </fieldset>
              <button type="button" x-show="definitions.length > 1" x-on:click="definitions.splice(i, 1)">Remove this definition</button>
              <button type="button" x-on:click="definitions.push('')">Add another definition</button>
            </li>
          </template>
        </ul>
      </fieldset>
    </div>
    <button type="submit">Save term</button>
  </form>
</x-layout>
