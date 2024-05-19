<x-layout title="Complete your profile">
  <h1 class="PageHeading">Complete your profile</h1>

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

  <x-form method="PATCH" action="{{ rroute('complete-profile.update') }}" class="flex flex-col mt-6 gap-y-5">
    <input type="hidden" name="_intent" value="complete">
    <div class="FormGroup">
      <label for="name" class="Label Label-text">What's your name?</label>
      <input
        type="text" name="name" id="name" value="{{ old('name') }}"
        required autofocus autocomplete="name"
        class="FormControl"
      />
    </div>
    <button type="submit" class="Button Button--primary self-start">Complete profile</button>
  </x-form>
</x-layout>
