<x-layout title="Log in">
  <x-form method="POST" action="{{ rroute('login') }}">
    <h1 class="PageHeading">Log in to Vocablog</h1>
    <div class="flex flex-col gap-1 mb-5">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required />
    </div>
    <button type="submit" class="underline">Send me a login link</button>
  </x-form>
</x-layout>
