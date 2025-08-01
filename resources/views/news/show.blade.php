@props (['news'])

<x-layout>
    @push ('styles')
        <link rel="stylesheet" href="{{asset('assets/css/news.css')}}">
    @endpush

    <x-slot name="title">{{ucwords($news->title)}} || Latest News </x-slot>

    <section class="singleNewsWrapper">
        <div class="news-route"><a href="{{route('home')}}">Home</a>&ensp;/&ensp; <a href="{{route('news')}}">News</a> &ensp; / &ensp; {{ucfirst($news->title)}} </div>
    </section>
</x-layout>