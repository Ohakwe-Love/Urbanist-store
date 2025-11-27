<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- csrf token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- title --}}
    <title>{{ $title ?? "Urbanist | Improve your Livelihood" }}</title>

    {{-- preloader styles --}}
    <style>
        /* preloader */
        .pre-loader-container {
            width: 100%;
            height: 100vh;
            background-color: var(--color-white);
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            transition: opacity 0.6s ease;
        }

        .pre-loader {
            text-align: center;
        }

        .pre-loader-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            background-color: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
            margin: auto;
        }

        .pre-loader-border {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 6px solid rgba(0, 0, 0, 0.1);
            border-top-color: var(--color-hover);
            /* spinner color */
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
            z-index: 1;
        }

        .pre-loader-image {
            width: 60px;
            object-fit: contain;
            animation: pulse 1.2s ease-in-out infinite;
            z-index: 2;
        }

        .pre-loader h1 {
            font-size: 40px;
            margin-top: 20px;
            color: var(--secondary-color);
        }

        .loader-tagline {
            font-size: 1rem;
            color: var(--color-hover);
            margin-top: 0.5rem;
            text-align: center;
            font-style: italic;
        }

        .fadeIn {
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }
    </style>

    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico')}}" type="image/x-icon">


    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;700&display=swap" rel="stylesheet">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- iziToast --}}
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">

    {{-- other styles --}}
    @stack('styles')

</head>

<body>

    {{-- preloader --}}
    <x-preloader />

    <main>
        <!-- top-bar -->
        <div class="top-bar">
            <div class="container">
                <div class="contact-info">
                    <a href="tel:+0123-456-789"><i class="fas fa-phone"></i> <span>+0123-456-789</span></a>
                    <a href="mailto:example@domain.com"><i class="fas fa-envelope"></i>
                        <span>example@domain.com</span></a>
                </div>

                <div class="promo-text">
                    <p>free Shipping on orders over $200. shop now!</p>
                </div>

                <div class="language-currency">
                    <div class="language">
                        <div class="language-click clicked-language clicked" data-value="eng">
                            <img src="{{asset('assets/images/flags/eng.svg')}}" alt="English">
                            <p>English <span><ion-icon name="caret-down-sharp"></ion-icon></span></p>
                        </div>
                        <div class="language-dropdown" data-set="1">
                            <div class="language-option">
                                <img src="{{asset('assets/images/flags/eng.svg')}}" alt="English">
                                <p>English</p>
                            </div>
                            <div class="language-option">
                                <img src="{{asset('assets/images/flags/fr.svg')}}" alt="French">
                                <p>French</p>
                            </div>
                            <div class="language-option">
                                <img src="{{asset('assets/images/flags/deu.svg')}}" alt="Deutsch">
                                <p>Deutsch</p>
                            </div>
                        </div>
                    </div>

                    <div class="currency">
                        <div class="currency-click clicked-currency clicked">
                            <img src="{{asset('assets/images/flags/us.svg')}}" alt="United States">
                            <p>United States | USD $ <span><ion-icon name="caret-down-sharp"></ion-icon></span></p>
                        </div>
                        <div class="currency-dropdown" data-set="1">
                            <div class="currency-option">
                                <img src="{{asset('assets/images/flags/us.svg')}}" alt="United States">
                                United States | USD $
                            </div>
                            <div class="currency-option">
                                <img src="{{asset('assets/images/flags/deu.svg')}}" alt="Europe">
                                Europe | EUR €
                            </div>
                            <div class="currency-option">
                                <img src="{{asset('assets/images/flags/eng.svg')}}" alt="United Kingdom">
                                United Kingdom | GBP £
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- top-bar end -->

        <!-- header -->
        <x-header />
        <!-- header end -->

        <!-- Search Popup -->
        <x-search-popup />
        <!-- search end -->

        <!-- Mobile Menu -->
        <x-mobile-menu />
        <!-- mobile menu end -->

        <!-- overlay -->
        <div class="overlay" id="overlay"></div>
        <!-- overlay end -->

        <!-- cart menu -->
        <x-cart-menu />
        <!-- cart menu end -->

        {{-- content --}}
        {{ $slot }}
        <!-- content end -->

        <!-- back to top -->
        <div class="back-to-top">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        </div>
        <!-- back to top end -->

        <!-- footer -->
        <x-footer />
        {{-- footer end --}}
    </main>

    {{-- script --}}
    <script>
        // preloader
        window.addEventListener('load', () => {
            const loader = document.querySelector('.pre-loader-container');
            loader.style.opacity = '0';
            loader.style.pointerEvents = 'none';
            setTimeout(() => loader.remove(), 1000);
        });
    </script>

    <!-- ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>


    <!-- scripts -->
    <script src="{{ asset('assets/js/script.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/wishlist.js') }}"></script>
    {{--
    <script src="{{ asset('assets/js/cart.js') }}?v={{ time() }}"></script> --}}
    <script src="{{ asset('assets/js/cart-manager.js') }}?v={{ time() }}"></script>

    {{-- Additional Scripts --}}
    @stack('scripts')

    {{-- iziToast --}}
    <script src="{{ asset('assets/js/iziToast.min.js') }}?v={{ time() }}"></script>

    @if (Session::has('success'))
        <script>
            "use strict";
            iziToast.success({
                message: "{{ session('success') }}",
                position: 'bottomRight'
            });
        </script>
    @endif

    @if (session()->has('notify'))
        @foreach (session('notify') as $msg)
            <script>
                "use strict";
                iziToast.{{ $msg[0] }}({
                    message: "{{ trans($msg[1]) }}",
                    position: "bottomRight"
                });
            </script>
        @endforeach
    @endif

    @if (Session::has('error'))
        <script>
            "use strict";
            iziToast.error({
                message: "{{ session('error') }}",
                position: 'bottomRight'
            });
        </script>
    @endif

    @if (@$errors->any())
        <script>
            "use strict";
            @foreach ($errors->all() as $error)
                iziToast.error({
                    message: "{{ __($error) }}",
                    position: "bottomRight"
                });
            @endforeach
        </script>
    @endif
</body>

</html>