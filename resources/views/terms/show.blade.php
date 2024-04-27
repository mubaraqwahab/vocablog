<x-layout title="{{ $term->term }}">
  <h1>{{ $term->term }}</h1>
  <p>{{ $term->lang->name }}</p>

  @php
    $defs = $term->definitions->map(function ($def) {
      $transformedDef = Arr::only($def->attributesToArray(), ['definition', 'comment']);
      $transformedDef['examples'] = [];
      foreach ($def->examples as $k => $e) {
        $transformedDef['examples'][$k] = $e->example;
      }
      return $transformedDef;
    });

    $emptyDef = ['definition' => '', 'examples' => [''], 'comment' => ''];
  @endphp


  <div x-data="{ defs: {{ Js::from($defs) }}, newlyAdded: null }">
    <h2>Definitions</h2>
    <ol>
      <template x-for="(def, i) in defs">
        <li x-data="{ readonly: newlyAdded !== 'definition' }">
          <div x-show="readonly">
            <p x-text="def.definition"></p>
            <strong>Examples</strong>
            <ul>
              <template x-for="example in def.examples">
                <li x-text="example"></li>
              </template>
            </ul>
            <div x-show="def.comment">
              <strong>Comment</strong>
              <p x-text="def.comment"></p>
            </div>
            <button type="button" x-on:click="readonly = false">Edit definition</button>
            <button type="button">Delete definition</button>
          </div>


          <x-form
            x-show="!readonly"
            method="PUT"
            :action="`{{ route('definitions.update', ['definition' => '{}']) }}`.replace('{}', def.id)"
          >
            <div>
              <label :for="`definition-${i}`">Definition</label>
              <textarea name="definition" :id="`definition-${i}`" x-model="def.definition" required></textarea>
            </div>
            <ul>
              <template x-for="(_, j) in def.examples">
                <li>
                  <label :for="`example-${i}-${j}`" x-text="`Example ${j+1}`"></label>
                  <input
                    type="text"
                    :id="`example-${i}-${j}`"
                    :name="`examples[${j}]`"
                    x-model="def.examples[j]"
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
                    })"
                  >
                    Add another example
                  </button>
                </li>
              </template>
            </ul>
            <div>
              <label :for="`comment-${i}`">Comment</label>
              <textarea name="comment" :id="`comment-${i}`" x-model="def.comment"></textarea>
            </div>
            <button type="submit">Save</button>
            <button type="button" x-on:click="readonly = true">Cancel</button>
          </x-form>
        </li>
      </template>
    </ol>
    <button
      type="button"
      x-on:click="(() => {
        defs.push({{ Js::from($emptyDef) }});
        newlyAdded = 'definition';
      })"
    >
      Add definition
    </button>
  </div>


  <button type="button">Delete term</button>
</x-layout>
