<!DOCTYPE html>
<html lang="en">
@include('_head')

<body class="bg-gray-300 font-sans antialiased">
    <div class="container block mx-auto px-2 py-4">
        @include('_nav')
        @if (session('status'))
            <div class="ml-4 mr-4 md:ml-28 md:mr-28 bg-white pt-2 pb-2 text-black border-4 border-b-8 border-green-500">
                <div class="ml-4 mr-4">
                    {{ session('status') }}
                </div>
            </div>
        @endif
        <a class="back-link" href="/">Back</a>
        <section class="movie-wrapper">
            <h1 class="movie-title">{{ $movie['title'] }}</h1>
            <div class="movie-controls">
                @if (Auth::check())
                    @if (!json_decode(Auth::user()->watchlist) || !in_array($movie['id'], json_decode(Auth::user()->watchlist)))
                        <a class="watchlist-link" href="/user/watchlist/add/{{ $movie['id'] }}">add to watchlist</a>
                    @elseif (in_array($movie['id'], json_decode(Auth::user()->watchlist)))
                        <a class="watchlist-link" href="/user/watchlist/remove/{{ $movie['id'] }}">remove from
                            watchlist</a>
                    @endif
                    @if (Auth::check() && Auth::user()->is_admin)
                        <a class="edit-link" href="/movies/{{ $movie['id'] }}/edit">edit movie</a>
                    @endif
                @endif
            </div>
            <p class="movie-year">Released <span class="bold-paragraph">{{ $movie['released'] }}</span></p>
            <p class="movie-rating">Rating <span class="bold-paragraph">{{ $movie['avg_rating'] }}/10</span> </p>
            <div class="movie-media">
                <img class="movie-poster" height="505" src="{{ $movie['urls_images'] }}" alt="movie poster" />
                <div class="iframe-wrapper">
                  <iframe class="movie-trailer" src="{{ $movie['url_trailer'] }}"
                    title="YouTube video player" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
                </div>
            </div>
            <p class="movie-genre">{{ $movie['genre'] }}</p>
            <div class="movie-cast">
                @foreach ($movie['cast'] as $actor)
                    <p>{{ $actor }}</p>
                @endforeach
            </div>
            <p class="movie-abstract">{{ $movie['abstract'] }}</p>
        </section>
        @include('_reviews')
    </div>
</body>

</html>
