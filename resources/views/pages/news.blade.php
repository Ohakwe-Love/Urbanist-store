<x-layout>
    <x-slot name="title">Latest News | Urbanist</x-slot>

    @push ('styles')
        <link rel="stylesheet" href="{{asset('assets/css/news.css')}}?v={{ time() }}">
    @endpush

    <section class="news-wrapper">
        <div class="news-route"><a href="{{route('home')}}">Home</a>&ensp;/&ensp; News</div>

        <h1 class="updates-heading">Updates from Urbanist</h1>
        {{-- <p>Stay updated with the latest news and announcements.</p> --}}

        <div class="trending-news-card">
            <a href="" class="trending-news-card-img">
                <img src="{{asset('assets/images/new-arrivals/new-1.webp')}}" alt="">
            </a>

            <div class="trending-news-card-details">
                <a href="" class="trending-news-card-title"><h2>Product title here</h2></a>
                <p class="trending-news-card-desc">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac erat ut neque bibendum egestas sed quis justo. Integer non rhoncus diam. Nullam eget dapibus lectus, vitae condimentum sem
                </p>

                <div class="trending-news-card-date-time-content">
                    <p>Date: <span>22 April 2023</span></p>
                    <p>Read time:<span>12min</span></p>
                </div>
            </div>
        </div>

        <div class="news-card-row">
           <x-news-card />
        </div>
    </section>
</x-layout>