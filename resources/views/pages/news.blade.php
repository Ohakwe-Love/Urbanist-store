<x-layout>
    <x-slot name="title">Latest News | Urbanist</x-slot>

    @push ('styles')
        <link rel="stylesheet" href="{{asset('assets/css/news.css')}}?v={{ time() }}">
    @endpush

    <section class="news-wrapper">
        <div><a href="{{route('home')}}">Home</a>&ensp;/&ensp; News</div>

        <h1>Updates from Urbanist</h1>
        {{-- <p>Stay updated with the latest news and announcements.</p> --}}

        <div class="trending-news-card">
            <div class="trending-news-img">
                <img src="" alt="">
            </div>
        </div>

        <div class="news-card-row">
            
        </div>
    </section>
</x-layout>