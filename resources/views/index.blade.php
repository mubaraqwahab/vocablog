<x-layout title="">
  <h1 class="font-extrabold mb-5 text-gray-900 text-4xl">{{ config('app.name') }}</h1>
  <p class="text-xl mb-6">{{ config('app.tagline') }}</p>

  <x-form method="POST" action="{{ rroute('login') }}" class="mb-14">
    <div class="FormGroup mb-5">
      <label for="email" class="Label Label-text">Enter your email to get started</label>
      <input type="email" name="email" id="email" required class="FormControl" />
    </div>
    <button type="submit" class="Button Button--primary">Send me a login link</button>
  </x-form>

  <p class="mb-1">With {{ config('app.name') }}, you can</p>
  <ul class="space-y-1 list-disc list-outside pl-6">
    <li>record the new words, phrases, etc. you learn</li>
    <li>get weekly email summaries of your new vocabulary</li>
    <li>take quizzes to test your knowledge.</li>
  </ul>

  <img src="/vocablog-screenshot.webp" alt="A screenshot of Vocablog" class="mt-10 border rounded-md shadow" />

  <p class="mt-16 text-sm text-gray-500">
    Built by <a href="https://mubaraqwahab.com" class="underline">Mubaraq Wahab</a> &middot;
    <a href="https://github.com/mubaraqwahab/vocablog" class="underline">Source code</a>
  </p>
</x-layout>
