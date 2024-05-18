<x-layout title="Log in">
  <h1 class="PageHeading">Log in to {{ config('app.name') }}</h1>
  <x-form method="POST" action="{{ rroute('login') }}" class="mt-6">
    <div class="FormGroup mb-5">
      <label for="email" class="Label Label-text">Email</label>
      <input type="email" name="email" id="email" required class="FormControl" />
    </div>
    <button type="submit" class="Button Button--primary">Send me a login link</button>
  </x-form>
</x-layout>
