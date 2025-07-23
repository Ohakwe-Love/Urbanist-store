@php
    $wishlists = auth()->user()->wishlists();
@endphp

<x-dashboard-layout :user="$user">
    <x-slot name="title">{{ucfirst($user->username)}}  | Urbanist Dashboard</x-slot> 

    <div class="dashboard-header">
        <div>
            <h1>Welcome back, {{ ucfirst(Auth::user()->username) }}!</h1>
            <p class="welcome-subtext">Manage your orders, wishlist, and account settings here.</p>
        </div>

        <x-dashboard-sidebar-toggle />
    </div>

    <div class="dashboard-content">
        {{-- stats --}}
        <div class="stats-row">
            <a class="stats-col" href="">
                <div class="stats-header">
                    <p>total orders</p>
                    <span class="stats-icon">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM6 9a1 1 0 112 0 1 1 0 01-2 0zm6 0a1 1 0 112 0 1 1 0 01-2 0z"/>
                        </svg>
                    </span>
                </div>
                <div class="stats-content">
                    <h1>300</h1>
                </div>
            </a>
            <a class="stats-col" href="{{route('wishlist')}}">
                <div class="stats-header">
                    <p>wishlist</p>
                    <span class="stats-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </span>
                </div>
                <div class="stats-content">
                    <h1>
                        @if ($wishlists)
                            {{ auth()->user()->wishlists()->count() }}
                        @else
                            0
                        @endif
                    </h1>
                </div>
            </a>
            <a class="stats-col" href="">
                <div class="stats-header">
                    <p>total payments</p>
                    <span class="stats-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </span>
                </div>
                <div class="stats-content">
                    <h1>50</h1>
                </div>
            </a>
        </div>

        <!-- Recent Orders -->
        <div class="recent-orders">
            <div class="recent-orders-header">
                <h2 class="dashboard-details-header">
                    <i class="fas fa-clock"></i>
                    Recent Orders
                </h2>
                <a href="" class="view-all">view all</a>
            </div>
            
            <div class="order-item">
                <div class="order-info">
                    <div class="order-date">May 28, 2025</div>
                    <div class="order-id">#ORD-2024-001</div>
                </div>
                <div class="order-amount">$76,997.00</div>
            </div>

            <div class="order-item">
                <div class="order-info">
                    <div class="order-date">May 28, 2025</div>
                    <div class="order-id">#ORD-2024-001</div>
                </div>
                <div class="order-amount">$76,997.00</div>
            </div>

            <div class="order-item">
                <div class="order-info">
                    <div class="order-date">May 28, 2025</div>
                    <div class="order-id">#ORD-2024-001</div>
                </div>
                <div class="order-amount">$76,997.00</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="quick-actions-header dashboard-details-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
                <h2>Quick Actions</h2>
            </div>
            
            <div class="quick-actions-row">
                <a href="{{route('shop')}}" class="quick-actions-col">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <div>shop now</div>
                </a>

                <a href="#" class="quick-actions-col">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <div>edit profile</div>
                </a>

                <a href="#" class="quick-actions-col">
                    <i class="fas fa-file-export"></i>
                    <div>share link</div>
                </a>
            </div>
        </div>
    </div>
</x-dashboard-layout>