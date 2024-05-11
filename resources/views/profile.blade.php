<x-layout title="Profile">
  <h1 class="PageHeading">Profile</h1>

  @if (session('status') === 'profile-updated')
    <div>
      <p>Profile updated</p>
    </div>
  @endif

  <x-form method="PATCH" action="{{ rroute('profile') }}" class="mt-6 space-y-6">
    <div class="flex flex-col gap-1 md:w-72">
      <label for="name">Name</label>
      <input type="text" name="name" id="name" required autocomplete="name" value="{{ old('name', Auth::user()->name) }}" />
    </div>
    <div class="flex flex-col gap-1 md:w-72">
      <label for="email">Email (readonly)</label>
      <input type="text" name="email" id="email" readonly autocomplete="email" value="{{ old('email', Auth::user()->email) }}" />
    </div>
    <button type="submit" class="Button Button--primary">Save</button>
  </x-form>
</x-layout>
