<x-layout title="Settings">
  <h1 class="PageHeading">Settings</h1>

  @if (session('status') === 'settings-updated')
    <div
      x-data="{ open: true }" x-show="open"
      class="flex justify-between items-center mb-4 border border-green-300 bg-green-100 text-green-700 rounded px-4 py-2"
    >
      <p>Settings updated</p>
      <button type="button" x-on:click="open = false;">Dismiss</button>
    </div>
  @endif

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

  <x-form method="PATCH" action="{{ rroute('settings.update') }}" class="space-y-5">
    <div class="FormGroup">
      <label for="name" class="Label Label-text">Name</label>
      <input
        type="text" name="name" id="name" autocomplete="name"
        value="{{ old('name', Auth::user()->name) }}"
        class="FormControl"
      />
    </div>
    <div class="FormGroup">
      <label for="email" class="Label">
        <span class="Label-text">Email</span>
        <span class="Label-helper">You can't edit this.</span>
      </label>
      <input
        type="text" id="email" readonly
        value="{{ Auth::user()->email }}"
        class="FormControl is-readonly"
      />
    </div>
    <div class="FormGroup FormGroup--horizontal">
      <input
        type="checkbox" name="weekly_digest_enabled" id="weekly_digest_enabled"
        @checked(old('weekly_digest_enabled', Auth::user()->weekly_digest_enabled))
        class="FormControl"
      />
      <label for="weekly_digest_enabled" class="Label">
        <span class="Label-text">Receive weekly digest</span>
        <span class="Label-helper">
          You'll receive at the end of every week an email summary
          of the new terms you learnt that week.
        </span>
      </label>
    </div>
    <button type="submit" class="Button Button--primary">Update profile</button>
  </x-form>
</x-layout>
