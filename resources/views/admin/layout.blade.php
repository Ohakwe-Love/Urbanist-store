<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin | Urbanist')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="admin-body">
    <div class="admin-shell">
        <button type="button" class="admin-sidebar-overlay" data-admin-sidebar-close aria-label="Close sidebar"></button>

        <aside class="admin-sidebar" id="adminSidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <small>Urbanist Store</small>
                <strong>Admin Panel</strong>
            </a>

            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-chart-line"></i> Dashboard</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-couch"></i> Products</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-layer-group"></i> Categories</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.tags.index') }}" class="{{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-tags"></i> Tags</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-bag-shopping"></i> Orders</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-credit-card"></i> Payments</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.shipments.index') }}" class="{{ request()->routeIs('admin.shipments.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-truck-fast"></i> Shipping</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-users"></i> Users</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-ticket"></i> Coupons</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.content.index') }}" class="{{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-pen-to-square"></i> Content</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.support.index') }}" class="{{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-envelope-open-text"></i> Support</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.newsletters.index') }}" class="{{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-paper-plane"></i> Newsletters</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-star"></i> Reviews</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-sliders"></i> Settings</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="{{ route('home') }}">
                    <span class="admin-nav-label"><i class="fa-solid fa-store"></i> Storefront</span>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit">
                        <span class="admin-nav-label"><i class="fa-solid fa-right-from-bracket"></i> Logout</span>
                        <i class="fa-solid fa-angle-right"></i>
                    </button>
                </form>
            </nav>
        </aside>

        <main class="admin-content">
            <div class="admin-topbar">
                <div>
                    <button type="button" class="admin-sidebar-toggle" data-admin-sidebar-toggle aria-controls="adminSidebar" aria-expanded="false" aria-label="Open sidebar">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <h1>@yield('heading', 'Admin')</h1>
                    <p>@yield('subheading', 'Manage the Urbanist store from one place.')</p>
                </div>
                <div class="admin-user-card">
                    <strong>{{ auth('admin')->user()->name }}</strong>
                    <span>{{ auth('admin')->user()->email }}</span>
                    <span>Admin account</span>
                    <form method="POST" action="{{ route('admin.logout') }}" style="margin-top:14px;">
                        @csrf
                        <button type="submit" class="btn btn-danger" style="width:100%;">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    @if (session('success'))
        <script>
            iziToast.success({ message: @json(session('success')), position: 'bottomRight' });
        </script>
    @endif
    @if (session('error'))
        <script>
            iziToast.error({ message: @json(session('error')), position: 'bottomRight' });
        </script>
    @endif
    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                iziToast.error({ message: @json($error), position: 'bottomRight' });
            @endforeach
        </script>
    @endif
    <script>
        (() => {
            const body = document.body;
            const toggle = document.querySelector('[data-admin-sidebar-toggle]');
            const closeTargets = document.querySelectorAll('[data-admin-sidebar-close]');
            const desktopBreakpoint = window.matchMedia('(min-width: 981px)');

            const syncDesktopState = () => {
                if (desktopBreakpoint.matches) {
                    body.classList.remove('admin-sidebar-open');
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                }
            };

            if (toggle) {
                toggle.addEventListener('click', () => {
                    const isOpen = body.classList.toggle('admin-sidebar-open');
                    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            }

            closeTargets.forEach((target) => {
                target.addEventListener('click', () => {
                    body.classList.remove('admin-sidebar-open');
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            });

            desktopBreakpoint.addEventListener('change', syncDesktopState);
            syncDesktopState();
        })();
    </script>
    @stack('scripts')
</body>
</html>
