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
      <h1 class="text-3xl font-bold">My vocabulary</h1>
      <a href="">New term</a>
      <form>
        <label for="query">Search terms</label>
        <input type="search" id="query" name="query" />
        <button type="submit">Search</button>
      </form>
      <p>Showing M out of N terms.</p>
      <ul>
        <li>
          <a href="">
            <strong>Term</strong>
            <span>Lang</span>
            <span>N defs</span>
          </a>
        </li>
      </ul>
    </div>
  </body>
</html>
