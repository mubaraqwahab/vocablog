<x-layout title="Quiz">
  <h1 class="PageHeading">Quiz</h1>

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
        return this.responses.filter((r, i) => r === this.questions[i].answerIndex).length;
      },
    }"
  >
    <template x-if="question">
      <form x-ref="questionForm" tabindex="-1">
        <p class="mb-5">
          <span class="block mb-1 text-sm text-gray-500" x-text="`Question ${currentIndex + 1} of ${questions.length}`"></span>
          <span class="block font-medium text-lg" x-html="`What does <b>${question.term}</b> mean in ${question.lang}?`"></span>
        </p>

        <template x-if="hasError">
          <div tabindex="-1" x-ref="errorFeedback" class="my-4">You need to choose an option.</div>
        </template>

        <div class="space-y-3">
          <template x-for="(option, i) in question.options" x-bind:key="`${question.term}-${i}`" hidden>
            <label
              x-bind:for="`choice-${i}`"
              class="flex gap-x-3 items-center border rounded px-3 py-2 hover:bg-gray-50"
              x-bind:class="response !== null && 'pointer-events-none'"
            >
              <input
                type="radio"
                name="choice"
                x-bind:value="i"
                x-bind:id="`choice-${i}`"
                x-model.number="tempResponse"
                required
                x-bind:readonly="response !== null"
                x-bind:class="response !== null && 'pointer-events-none text-gray-500'"
              />
              <span class="flex-grow" x-text="`${letter(i)}. ${option}`"></span>
            </label>
          </template>
        </div>

        <template x-if="response !== null">
          <div
            tabindex="-1"
            x-ref="responseFeedback"
            x-html="
              response === question.answerIndex
                ? 'Correct!'
                : `Nah! The correct answer is <b>${letter(question.answerIndex)}</b>.`
                + ` <span class='sr-only' x-text='question.options[question.answerIndex]'></span>`
            "
            class="my-4 border rounded px-4 py-2"
            x-bind:class="
              response === question.answerIndex
                ? 'border-green-300 bg-green-100 text-green-700'
                : 'border-red-300 bg-red-100 text-red-700'
            "
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
            x-show="response === null"
          >
            Submit
          </button>

          <button
            type="button"
            class="Button Button--primary"
            @click="() => {
              tempResponse = '';
              currentIndex++;
              $nextTick(() => {
                $refs.questionForm.focus();
              });
            }"
            x-cloak
            x-show="response !== null"
          >
            Next
          </button>
        </div>
      </form>
    </template>

    <template x-if="!question">
      <div>
        <p class="text-lg font-medium">All done!</p>
        <p x-html="`You got <b>${correctResponseCount}</b> of <b>${questions.length}</b> terms correct.`"></p>
        <a href="" class="Button Button--secondary mt-6">Play again</a>
      </div>
    </template>
  </div>
</x-layout>
