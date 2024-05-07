<x-layout title="Log in">
  <x-form method="POST" action="{{ rroute('login.submit') }}">
    <div>
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required />
    </div>
    <button type="submit">Send me a login link</button>
  </x-form>
</x-layout>
