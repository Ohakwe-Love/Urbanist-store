<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    {{-- title --}}
    <title>{{ $title ?? "Urbanist | Improve your Livelihood" }}</title>

    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}?v={{ time() }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico')}}" type="image/x-icon">


    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible+Next:ital,wght@0,200..800;1,200..800&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Outfit:wght@100..900&family=Oxygen:wght@300;400;700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Urbanist:ital,wght@0,100..900;1,100..900&family=Young+Serif&display=swap" rel="stylesheet">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- iziToast --}}
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}?v={{ time() }}">
</head>
<body>

    <main class="form-wrapper">
        {{ $slot }}
    </main>


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