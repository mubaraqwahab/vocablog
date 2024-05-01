<x-layout title="{{ $term->term }}">
  <a href="{{ route('terms.index') }}">Back to terms</a>
  <h1>{{ $term->term }}</h1>
  <p>{{ $term->lang->name }}</p>

  @php
    $defs = $term->definitions->map(function ($def) {
      $transformedDef = Arr::only($def->attributesToArray(), ['id', 'definition', 'comment']);
      $transformedDef['examples'] = [];
      foreach ($def->examples as $k => $e) {
        $transformedDef['examples'][$k] = $e->example;
      }
      return $transformedDef;
    });
  @endphp

  <div
    x-data="{
      defs: {{ Js::from($defs) }},
      // prevDefs may be a sparse array.
      prevDefs: [],
      newlyAddedThing: null,
      updateDefRouteTemplate: {{
        Js::from(
          // -1 serves as a placeholder for the actual definition ID.
          route('definitions.upsert', ['term' => $term, 'definition' => -1], absolute: false)
        )
      }},
      deleteDefRouteTemplate: {{
        Js::from(
          route('definitions.destroy', ['definition' => -1], absolute: false)
        )
      }},
    }"
  >
    <h2>Definitions</h2>
    <ol>
      <template x-for="(def, i) in defs">
        {{-- NOTE:
          1. Newly added defs won't have an ID yet, so their def.id would be undefined.
          2. When def.id isn't undefined, it will be > 0 (because MySQL auto_increment
             starts at 1 by default), so it's safe to do !!def.id and similar.
        --}}
        <li x-data="{ readonly: !!def.id }">
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
            <button
              type="button"
              x-bind:id="`edit-${i}`"
              x-on:click="
                readonly = false;
                // Save a clone of the current def
                prevDefs[i] = JSON.parse(JSON.stringify(def));
              "
              x-init="
                $watch('readonly', (readonly) => {
                  console.log({ $el, readonly })
                  if (readonly) {
                    $nextTick(() => $el.focus());
                  }
                });
              "
            >
              Edit definition
            </button>
            <button type="button" x-on:click="$refs.deleteDefDialog.showModal()">Delete definition</button>
            <dialog x-ref="deleteDefDialog">
              <p>Are you sure you want to delete this definition?</p>
              <x-form method="DELETE" x-bind:action="deleteDefRouteTemplate.replace(/-1$/, def.id || '')">
                <button type="submit" formmethod="dialog">No, keep it</button>
                <button type="submit">Yes, delete it</button>
              </x-form>
            </dialog>
          </div>

          <x-form
            x-show="!readonly"
            x-bind:action="updateDefRouteTemplate.replace(/-1$/, def.id || '')"
            method="PUT"
          >
            <div>
              <label x-bind:for="`definition-${i}`">Definition</label>
              <textarea
                name="definition"
                x-bind:id="`definition-${i}`"
                x-model="def.definition"
                x-init="
                  if (!def.id) {
                    console.log({ def })
                    $nextTick(() => $el.focus());
                  }
                  $watch('readonly', (readonly) => {
                    console.log({ $el, readonly })
                    if (!readonly) {
                      $nextTick(() => $el.focus());
                    }
                  });
                "
                required
                maxlength="255"
              ></textarea>
            </div>
            <ul>
              <template x-for="(_, j) in def.examples">
                <li>
                  <label x-bind:for="`example-${i}-${j}`" x-text="`Example ${j+1}`"></label>
                  <input
                    type="text"
                    x-bind:id="`example-${i}-${j}`"
                    x-bind:name="`examples[${j}]`"
                    x-model="def.examples[j]"
                    required
                    maxlength="255"
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
                    x-on:click="
                      def.examples.push('');
                      newlyAddedThing = 'example';
                    "
                  >
                    Add another example
                  </button>
                </li>
              </template>
            </ul>
            <div>
              <label x-bind:for="`comment-${i}`">Comment</label>
              <textarea
                name="comment"
                x-bind:id="`comment-${i}`"
                x-model="def.comment"
                maxlength="255"
              ></textarea>
            </div>
            <button type="submit">Save</button>
            <button
              type="button"
              x-on:click="
                if (def.id) {
                  readonly = true;
                  // Reset the def to what is was before editing.
                  defs[i] = prevDefs[i];
                  delete prevDefs[i];
                } else {
                  // Remove the new def.
                  defs.splice(i, 1);
                }
              "
            >
              Cancel
            </button>
          </x-form>
        </li>
      </template>
    </ol>
    <button
      type="button"
      x-on:click="
        defs.push({ definition: '', examples: [''], comment: '' });
        newlyAddedThing = 'definition';
      "
    >
      Add definition
    </button>
  </div>

  <div x-data>
    <button type="button" x-on:click="$refs.deleteTermDialog.showModal()">Delete term</button>
    <dialog x-ref="deleteTermDialog">
      <p>Are you sure you want to delete this term?</p>
      <x-form method="DELETE" action="{{ route('terms.destroy', ['term' => $term], absolute: false) }}">
        <button type="submit" formmethod="dialog">No, keep it</button>
        <button type="submit">Yes, delete it</button>
      </x-form>
  </div>
  </dialog>

</x-layout>
