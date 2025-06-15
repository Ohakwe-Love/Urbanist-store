@props(['user'])

<x-layout>
    @push('styles')
        <link rel="stylesheet" href="{{asset('assets/css/dashboard.css')}}?v={{ time() }}">
    @endpush

    <section class="dashboard-container">
        <div class="dashboard-sidebar">
            <a href="{{route('dashboard')}}" class="dashboard-link {{ Route::is('dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dashboard-icon">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                dashboard
            </a>
            <a href="" class="dashboard-link {{ Route::is('orders') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="orders-icon">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                orders
            </a>
            <a href="{{route('wishlist')}}" class="dashboard-link {{ Route::is('wishlist') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="wishlist-icon">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                wishlists
            </a>
            <a href="" class="dashboard-link {{ Route::is('addresses') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="addresses-icon">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                addresses
            </a>
            <a href="" class="dashboard-link {{ Route::is('payments') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="payment-icon">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
                payments
            </a>
            <a href="" class="dashboard-link {{ Route::is('profile') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="profile-icon">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                profile
            </a>
            <form method="POST" action="{{ route('logout') }}" class="logout" title="Logout">
                @csrf
                <button type="submit" class="user-action-tag">
                    <span class="user-action-tag-icon"><i class="fa fa-sign-out"></i></span> 
                    Logout
                </button>
            </form>
        </div>

        {{--  --}}
        <div class="dashboard-overlay"></div>

        <div class="dashboard-main">
            {{$slot}}
        </div>

        {{-- js --}}
        @push('scripts')
            <script src="{{asset('assets/js/dashboard.js')}}?v={{ time() }}"></script>
        @endpush
    </section>
</x-layout>