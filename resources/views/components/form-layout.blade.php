<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

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
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}?v={{ time() }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico')}}" type="image/x-icon">


    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible+Next:ital,wght@0,200..800;1,200..800&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Outfit:wght@100..900&family=Oxygen:wght@300;400;700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Urbanist:ital,wght@0,100..900;1,100..900&family=Young+Serif&display=swap"
        rel="stylesheet">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- iziToast --}}
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}?v={{ time() }}">
</head>

<body>

    {{-- preloader --}}
    <x-preloader />

    <main class="form-wrapper">
        {{ $slot }}
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


    {{-- iziToast --}}
    <script src="{{ asset('assets/js/iziToast.min.js') }}?v={{ time() }}"></script>

    {{-- js --}}
    <script src="{{ asset('assets/js/form.js') }}?v={{ time() }}"></script>

    @if (Session::has('success'))
        <script>
            "use strict";
            iziToast.success({
                message: "{{ session('success') }}",
                position: 'topRight'
            });
        </script>
    @endif

    @if (session()->has('notify'))
        @foreach (session('notify') as $msg)
            <script>
                "use strict";
                iziToast.{{ $msg[0] }}({
                    message: "{{ trans($msg[1]) }}",
                    position: "topRight"
                });
            </script>
        @endforeach
    @endif

    @if (Session::has('error'))
        <script>
            "use strict";
            iziToast.error({
                message: "{{ session('error') }}",
                position: 'topRight'
            });
        </script>
    @endif

    @if (@$errors->any())
        <script>
            "use strict";
            @foreach ($errors->all() as $error)
                iziToast.error({
                    message: "{{ __($error) }}",
                    position: "topRight"
                });
            @endforeach
        </script>
    @endif
</body>

</html>