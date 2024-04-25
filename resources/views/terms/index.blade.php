<x-layout title="My vocabulary">
  <div class="container">
    <h1 class="text-3xl font-bold">My vocabulary</h1>
    <a href="">New term</a>
    <form>
      <label for="query">Search terms</label>
      <input type="search" id="query" name="query" />
      <button type="submit">Search</button>
    </form>
    <p>Showing M out of N terms.</p>
    {{ $terms }}
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
</x-layout>
