<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Vocabulary | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite('resources/css/app.css')
  </head>
  <body>
    <div class="container">
      <h1 class="text-3xl font-bold">Term</h1>
      <p>Language</p>
      <a href="">Edit term</a>
      <form>
        <button type="submit">Delete term</button>
      </form>
      <h2>Definitions</h2>
      <button type="button">Add definition</button>
      <ul>
        <li>
          <details>
            <summary>Definition 1</summary>
            <div>
              <button type="button">Edit definition</button>
              <button type="button">Delete definition</button>
              <p>Definition</p>
              <h3>Examples</h3>
              <ul>
                <li>Example 1</li>
              </ul>
              <h3>Comment</h3>
              <p>Comment</p>
            </div>
          </details>
        </li>
      </ul>
    </div>
  </body>
</html>
