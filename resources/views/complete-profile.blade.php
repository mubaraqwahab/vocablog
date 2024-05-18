<x-layout title="Complete your profile">
  <h1 class="PageHeading">Complete your profile</h1>

  <x-form method="PATCH" action="{{ rroute('profile.update') }}" class="flex flex-col mt-6 gap-y-5">
    <input type="hidden" name="_intent" value="complete">
    <div class="FormGroup">
      <label for="name" class="Label Label-text">What's your name?</label>
      <input
        type="text" name="name" id="name" value="{{ old('name') }}"
        required autofocus autocomplete="name"
        class="FormControl"
      />
    </div>
    <button type="submit" class="Button Button--primary">Complete profile</button>
  </x-form>
</x-layout>
