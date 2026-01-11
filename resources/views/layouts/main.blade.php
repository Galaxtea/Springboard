<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <!-- Bootstrap Mins -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"> -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script> -->


        <!-- If there is a given title, add it on, otherwise ignore the dash -->
        <title>Springboard @hasSection('title') - @yield('title')@endif</title>

        <!-- Background based on location/event -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @include('layouts.partials.navbar')
        <div class="wrapper">
            <div class="content">
                <div class="crumbs">
                    @hasSection('crumbs')
                        @yield('crumbs')
                    @else
                        {{ Breadcrumbs::render('home') }}
                    @endif
                </div>
                <div class="alerts"></div>
                @yield('content')
            </div>
            @include('layouts.partials.sidebar')
        </div>
        @include('layouts.partials.footer')
    </body>

    @stack('scripts')
</html>