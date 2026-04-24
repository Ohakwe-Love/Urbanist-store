<x-layout>
    <x-slot name="title">Latest News | Urbanist</x-slot>

    @push ('styles')
        <link rel="stylesheet" href="{{asset('assets/css/news.css')}}?v={{ time() }}">
    @endpush

    <section class="news-wrapper">
        <div class="page-route"><a href="{{route('home')}}">Home</a>&ensp;/&ensp; News</div>
        <form action="{{ route('news') }}" method="GET" class="news-search-form">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search articles, topics, or dates">
            <button type="submit">Search</button>
            @if (request('search'))
                <a href="{{ route('news') }}" class="news-search-reset">Reset</a>
            @endif
        </form>
        
        @if ($trendingNews)
            <h1 class="updates-heading">{{ request('search') ? 'News Search Results' : 'Updates from Urbanist' }}</h1>
            <div class="trending-news-card">
                <a href="{{route('news.show', $trendingNews->slug)}}" class="trending-news-card-img">
                    <img src="{{ asset( $trendingNews->news_image)}}" alt="{{$trendingNews->slug}}">
                </a>

                <div class="trending-news-card-details">
                    <a href="{{route('news.show', $trendingNews->slug)}}" class="trending-news-card-title"><h2>{{ $trendingNews->title }}</h2></a>
                    <p class="trending-news-card-desc">
                        {{Str::limit($trendingNews->description, 100, "...")}}
                    </p>

                    <div class="trending-news-card-date-time-content">
                        <p>Date: <span>{{$trendingNews->date}}</span></p>
                        <p>Read time:<span>{{$trendingNews->read_time}}min</span></p>
                    </div>
                </div>
            </div>
        @endif
        
        @if($news->count())
            <div class="news-card-row">
                @foreach($news as $card)
                    <x-news-card :news="$card" />
                @endforeach
            </div>
        @else
            <div class="noBlogContainer">
                <div class="noBlogImage">
                    <img src="{{ asset('assets/images/icons/no-news.svg') }}" alt="No news available">
                </div>
                <h2>Oooops! No news found</h2>
                <p>Current news will be uploaded soon. Stay tuned!</p>
            </div>
        @endif

        <div class="blog-pagination-wrapper">
            {{ $news->links("vendor.pagination.default") }}
        </div>
    </section>
</x-layout>
