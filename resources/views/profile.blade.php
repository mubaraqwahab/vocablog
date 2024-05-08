<x-layout title="Profile">
  <h1 class="PageHeading">Profile</h1>

  @if (session('status') === 'profile-updated')
    <div>
      <p>Updated</p>
    </div>
  @endif

  <x-form method="PATCH" action="{{ rroute('profile.update') }}" class="mt-6 space-y-6">
    <div>
      <label for="name">Name</label>
      <input type="text" name="name" id="name" required autocomplete="name" value="{{ old('name', $user->name) }}" />
    </div>
    <div>
      <label for="email">Email</label>
      <input type="text" name="email" id="email" readonly autocomplete="email" value="{{ old('email', $user->email) }}" />
    </div>
    <button type="submit" class="underline">Update</button>
  </x-form>
</x-layout>
