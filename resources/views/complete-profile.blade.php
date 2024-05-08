<x-layout title="Complete your profile">
  <h1 class="PageHeading">Complete your profile</h1>

  <x-form method="PATCH" action="{{ rroute('complete-profile') }}" class="mt-6 space-y-6">
    <div class="flex flex-col gap-1 mb-5 md:w-72">
      <label for="name">What's your name?</label>
      <input type="text" name="name" id="name" required autofocus autocomplete="name" value="{{ old('name') }}" />
    </div>
    <button type="submit" class="underline">Save</button>
  </x-form>
</x-layout>
