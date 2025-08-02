@props (['news'])

<x-layout>
    @push ('styles')
        <link rel="stylesheet" href="{{asset('assets/css/news.css')}}?v={{ time() }}">
    @endpush

    <x-slot name="title">{{ucwords($news->title)}} || Latest News </x-slot>

    <section class="singleNewsWrapper">
        <div class="news-route"><a href="{{route('home')}}">Home</a>&ensp;/&ensp; <a href="{{route('news')}}">News</a> &ensp; / &ensp; {{ucfirst($news->title)}} </div>

        <h1 class="updates-heading">Topic: {{ucfirst($news->title)}}</h1>

        <div class="singleNewsContainer">

            <div class="singleNewsDetails">
                <div class="newsAuthor">
                    <div class="newsAuthorImg">
                        <img src="{{ asset("storage/avatars/urbanist.png") }}" alt="The Author">
                    </div>

                    <p>The Author</p>
                </div>

                <div class="singleNewsDateContainer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M9 1V3H15V1H17V3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3H7V1H9ZM20 11H4V19H20V11ZM8 13V15H6V13H8ZM13 13V15H11V13H13ZM18 13V15H16V13H18ZM7 5H4V9H20V5H17V7H15V5H9V7H7V5Z"></path></svg>
                    <div>
                        {{ $news->date }}
                    </div>
                </div>
            </div>

            <div class="singleNewsArticle">
                <article>
                    <div class="singleNewsImage">
                        <img src="{{ asset($news->news_image) }}" alt="{{ $news->slug }}">
                    </div>

                    <div class="singleNewsDetails">
                        <p>
                            {!! $news->description !!}
                        </p>
                    </div>
                </article>

                <div class="recentPosts">
                    <h2>Recent Posts</h2>
                    <ul>
                        @foreach($recentPosts as $post)
                            <li>
                                <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M16 12L10 18V6L16 12Z"></path></svg></span>

                                <a href="{{ route('news.show', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    
                </div>
            </div>

            <div class="news-card-row">
                @foreach($relatedNews as $card)
                    <x-news-card :news="$card" />
                @endforeach
            </div>
        </div>
    </section>
</x-layout>