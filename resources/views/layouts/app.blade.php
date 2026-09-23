<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Styles -->

    <style>
        span {
            line-height: normal !important;
        }

        span {
            font-size: 100% !important;
        }

        @media screen and (max-width: 32em) {
            body,
            button {
                font-size: 100% !important;
            }
        }
    </style>
    @include('back-partials.head')
</head>
<body id="app-body" style="background-color:#d2d6de;">
    {{--  --}}
    @include('partials.header')

    @yield('content')

    @include('back-partials.javascript')
    {{-- @include('partials.footer') --}}
</body>
</html>
