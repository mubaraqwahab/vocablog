<x-layout title="Quiz">
  <h1 class="PageHeading">Quiz</h1>

  @php(dump($questions))

  <div
    x-data="{
      questions: {{ Js::from($questions) }},
      responses: {{ Js::from(array_fill(0, count($questions), null)) }},
      currentIndex: 0,
      tempResponse: '',
      hasError: false,
      get question() {
        return this.questions[this.currentIndex];
      },
      get response() {
        return this.responses[this.currentIndex];
      },
      letter(i) {
        return String.fromCharCode(65 + i)
      },
      get correctResponseCount() {
        return this.responses.filter((r, i) => r === this.questions[i].answer).length;
      },
    }"
  >
    <form x-show="question">
      <p class="mb-4">
        <span class="block text-sm text-gray-500" x-text="`Question ${currentIndex + 1} of ${questions.length}`"></span>
        <span class="block font-medium text-lg" x-html="`What does <b>${question.term}</b> mean in ${question.lang}?`"></span>
      </p>

      <template x-if="hasError">
        <div tabindex="-1" x-ref="errorFeedback" class="my-4">You need to choose an option.</div>
      </template>

      <div class="space-y-3">
        <template x-for="(option, i) in question.options" hidden>
          <label x-bind:for="`choice-${i}`" class="flex gap-x-3 items-center border rounded px-3 py-2 hover:bg-gray-50">
            <input type="radio" name="choice" x-bind:value="option" x-bind:id="`choice-${i}`" x-model="tempResponse" required />
            <span class="flex-grow" x-text="`${letter(i)}. ${option}`"></span>
          </label>
        </template>
      </div>

      <template x-if="response">
        <div
          tabindex="-1"
          x-ref="responseFeedback"
          x-html="
            response === question.answer
              ? 'Correct!'
              : `Nah! The correct answer is <b>${letter(question.answerIndex)}</b>.`
              + ` <span class='sr-only' x-text='question.answer'></span>`
          "
          class="my-4"
        ></div>
      </template>

      <div class="mt-6">
        <button
          type="submit"
          class="Button Button--primary"
          @click.prevent="() => {
            if ($el.form.checkValidity()) {
              hasError = false;
              responses[currentIndex] = tempResponse;
              $nextTick(() => {
                $refs.responseFeedback.focus();
              });
            } else {
              hasError = true;
              $nextTick(() => {
                $refs.errorFeedback.focus();
              });
            }
          }"
          x-show="!response"
        >
          Submit
        </button>

        <button
          type="button"
          class="Button Button--primary"
          @click="() => {
            tempResponse = '';
            currentIndex++;
          }"
          x-cloak
          x-show="response"
        >
          Next
        </button>
      </div>
    </form>

    <div x-cloak x-show="!question">
      <p x-text="`All done! You got ${correctResponseCount} out of ${questions.length} terms correct.`"></p>
      <a href="" class="Button Button--secondary mt-6">Play again</a>
    </div>
  </div>
</x-layout>
