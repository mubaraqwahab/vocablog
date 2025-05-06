<x-layout title="Quiz">
  <h1 class="PageHeading">Quiz</h1>

  @php
    $responses = array_fill(0, count($questions), null)
  @endphp

  <div
    x-data="{
      questions: {{ Js::from($questions) }},
      responses: {{ Js::from($responses) }},
      currentIndex: 0,
      tempResponse: '',
      get question() {
        return this.questions[this.currentIndex];
      },
      get response() {
        return this.responses[this.currentIndex];
      },
    }"
  >
    <div x-show="!!question">
      <p class="text-lg font-medium mb-4">
        <small class="block" x-text="`Question ${currentIndex + 1} of ${questions.length}`"></small>
        <span class="block" x-html="`What does <b>${question.term}</b> mean in ${question.lang}?`"></span>
      </p>

      <template x-if="response">
        <div x-text="response === question.answer ? 'Correct!' : 'Nah, try again!'" class="my-4"></div>
      </template>

      <div class="space-y-3">
        <template x-for="(option, i) in question.options" hidden>
          <div class="flex gap-x-3 items-center">
            <input type="radio" name="choice" x-bind:id="`choice-${i}`" x-bind:checked="response === question.answer" />
            <label x-bind:for="`choice-${i}`" class="flex-grow" x-text="option"></label>
          </div>
        </template>
      </div>

      <div class="mt-6">
        <button class="Button Button--secondary" @click="currentIndex++">Previous</button>
        <button class="Button Button--secondary" @click="responses[currentIndex] = option">Check</button>
        <button class="Button Button--secondary" @click="currentIndex++">Next</button>
      </div>
    </div>

    <div x-cloak x-show="!question">
      <p>All done!</p>
      <a href="" class="Button Button--secondary mt-6">Play again</a>
    </div>
  </div>
</x-layout>
