@extends('admin.layout')

@section('title', 'Products | Admin')
@section('heading', 'Product Management')
@section('subheading', 'Create, price, feature, and restock products from one place.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <form method="GET" class="admin-inline-actions">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products">
                <button class="btn btn-secondary" type="submit">Search</button>
            </form>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add product</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Flags</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="admin-inline-actions">
                                    <img src="{{ $product->display_image_url }}" alt="{{ $product->title }}" class="thumb">
                                    <div>
                                        <strong>{{ $product->title }}</strong>
                                        <div class="helper-text">{{ $product->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->categoryRelation?->name ?? $product->category }}</td>
                            <td>
                                <strong>${{ number_format($product->price, 2) }}</strong>
                                @if ($product->sale_price)
                                    <div class="helper-text">Sale: ${{ number_format($product->sale_price, 2) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $product->stock_quantity <= 5 ? 'badge-warning' : 'badge-success' }}">
                                    {{ $product->stock_quantity }} in stock
                                </span>
                            </td>
                            <td class="admin-inline-actions">
                                @if ($product->is_featured)<span class="badge badge-neutral">Featured</span>@endif
                                @if ($product->is_new)<span class="badge badge-success">New</span>@endif
                            </td>
                            <td>
                                <div class="admin-actions">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn-link">Edit</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-link" onclick="return confirm('Delete this product?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><div class="empty-state">No products found yet.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $products->links() }}</div>
    </div>
@endsection
