@props(['products'])

<div class="products-row view-3">
    @forelse($products as $product)
        <x-product-card
            :product="$product"
            :productId="$product->id"
            :title="$product->title"
            :image="$product->image_url"
            :description="$product->description"
            :newPrice="$product->new_price ?? $product->price"
            :oldPrice="$product->old_price ?? ($product->sale_price ? $product->price : null)"
            :discount="$product->discount"
            :link="route('show', $product->slug)"
            :inStock="$product->in_stock ?? ($product->stock_quantity > 0)"
            :category="$product->category"
            :size="$product->size"
        />
    @empty
    
    @endForelse
</div>