<div class="search-popup" id="search-popup">
    <button class="close-search" id="close-search">
        <i class="fas fa-times"></i>
    </button>
    
    <div class="search-container">
        <form class="search-form" action="{{ route('shop') }}" method="GET">
            <input type="text" name="search" placeholder="Perform a search" value="{{ request('search') }}">
            <button type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>
    
        <div class="hot-searches">
            <h3>HOT SEARCHES:</h3>
            <div class="search-tags">
                <a href="{{route('shop', ['category' => 'Home Decoration'])}}" class="search-tag">Home Decoration</a>
                <a href="{{route('shop',  ['category' => 'Living Room'])}}" class="search-tag">Living Room</a>
                <a href="{{route('shop', ['category' => 'Bedroom'])}}" class="search-tag">Bedroom</a>
                <a href="{{route('shop', ['category' => 'Flower Vase'])}}" class="search-tag">Flower Vase</a>
                <a href="{{route('shop', ['category' => 'Kitchen'])}}" class="search-tag">Kitchen</a>
                <a href="{{route('shop', ['category' => 'Wooden Chair'])}}" class="search-tag">Wooden Chair</a>
            </div>
        </div>
    </div>
</div>