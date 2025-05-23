<x-layout title="">
  <h1 class="font-extrabold mb-5 text-gray-900 text-4xl">{{ config('app.name') }}</h1>
  <p class="text-xl mb-6">{{ config('app.tagline') }}</p>
  <p class="mb-1">With {{ config('app.name') }}, you can</p>
  <ul class="space-y-1 list-disc list-outside pl-6">
    <li>record the new words, phrases, etc. you learn</li>
    <li>get weekly email summaries of your new vocabulary</li>
    <li>take quizzes to test your knowledge.</li>
  </ul>
  <x-form method="POST" action="{{ rroute('login') }}" class="mt-9">
    <div class="FormGroup mb-5">
      <label for="email" class="Label Label-text">Enter your email to get started</label>
      <input type="email" name="email" id="email" required class="FormControl" />
    </div>
    <button type="submit" class="Button Button--primary">Send me a login link</button>
  </x-form>

  {{-- TODO: Add author and source links here --}}
</x-layout>
