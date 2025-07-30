@props (['news'])

<div class="news-card">
    <a href="" class="news-card-img">
        <img src="{{asset($news->news_image )}}" alt="">
    </a>

    <div class="news-card-details">
        <a href="" class="news-card-title"><h2>{{$news->title}}</h2></a>
        <p class="news-card-desc">
           {{Str::limit($news->description, 80)}}
        </p>

        <div class="news-card-date-time-content">
            <p>Date: <span>{{$news->date}}</span></p>
            <p>Read time:<span>{{$news->read_time}}</span><span>min</span></p>
        </div>
    </div>
</div>