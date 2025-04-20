<x-layout title="">
  <h1 class="font-extrabold mb-5 text-gray-900 text-4xl">{{ config('app.name') }}</h1>
  <p class="text-xl mb-8">A simple app to keep track of new words you learn.</p>
  <x-form method="POST" action="{{ rroute('login') }}">
    <div class="FormGroup mb-5">
      <label for="email" class="Label Label-text">Enter your email to get started</label>
      <input type="email" name="email" id="email" required class="FormControl" />
    </div>
    <button type="submit" class="Button Button--primary">Send me a login link</button>
  </x-form>
</x-layout>
