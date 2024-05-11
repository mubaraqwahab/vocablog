<x-layout title="Profile">
  <h1 class="PageHeading">Profile</h1>

  @if (session('status') === 'profile-updated')
    <div
      x-data="{ open: true }" x-show="open"
      class="flex justify-between items-center mb-4 border border-green-300 bg-green-100 text-green-700 rounded px-4 py-2"
    >
      <p>Profile updated</p>
      <button type="button" x-on:click="open = false;">Dismiss</button>
    </div>
  @endif

  <x-form method="PATCH" action="{{ rroute('profile.update') }}" class="space-y-5">
    <div class="FormGroup">
      <label for="name" class="Label Label-text">Name</label>
      <input
        type="text" name="name" id="name" required autocomplete="name"
        value="{{ old('name', Auth::user()->name) }}"
        class="FormControl"
      />
    </div>
    <div class="FormGroup">
      <label for="email" class="Label Label-text">Email (readonly)</label>
      <input
        type="text" name="email" id="email" readonly autocomplete="email"
        value="{{ old('email', Auth::user()->email) }}"
        class="FormControl is-readonly"
      />
    </div>
    <button type="submit" class="Button Button--primary">Update profile</button>
  </x-form>
</x-layout>
