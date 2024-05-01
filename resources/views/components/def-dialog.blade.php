@props(['termId', 'defData'])

@php
  $id = $defData['id'] ?? 0;
@endphp

<dialog
  x-data="{ def: {{ Js::from($defData) }} }"
  x-on:close="def = {{ Js::from($defData) }}"
  {{ $attributes }}
>
  <x-form
    action="{{ route('definitions.upsert', ['term' => $termId, 'definition' => $id ?: null]) }}"
    method="PUT"
  >
    <div>
      <label for="definition-{{ $id }}">Definition</label>
      <textarea
        name="definition"
        id="definition-{{ $id }}"
        required
        maxlength="255"
        x-bind:value="def.definition"
        x-on:keydown.enter.prevent
      ></textarea>
    </div>
    <ul>
      <template x-for="(_, j) in def.examples">
        <li>
          <label x-bind:for="`example-{{ $id }}-${j}`" x-text="`Example ${j+1}`"></label>
          <input
            type="text"
            x-bind:id="`example-{{ $id }}-${j}`"
            x-bind:name="`examples[${j}]`"
            x-model="def.examples[j]"
            x-init="
              if ((j === def.examples.length - 1) && (def.examples[j] === '')) {
                $el.focus()
              }
            "
            required
            maxlength="255"
          />
          <template x-if="def.examples.length > 1">
            <button type="button" x-on:click="def.examples.splice(j, 1)">
              Remove this example
            </button>
          </template>
          <template x-if="j === def.examples.length - 1">
            <button type="button" x-on:click="def.examples.push('')">
              Add another example
            </button>
          </template>
        </li>
      </template>
    </ul>
    <div>
      <label x-bind:for="`comment-{{ $id }}`">Comment</label>
      <textarea
        name="comment"
        id="comment-{{ $id }}"
        maxlength="255"
        x-bind:value="def.comment"
        x-on:keydown.enter.prevent
      ></textarea>
    </div>
    <button type="submit">Save</button>
    <button type="submit" formmethod="dialog" formnovalidate>Cancel</button>
  </x-form>
</dialog>
