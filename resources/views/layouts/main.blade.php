<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('trix/trix.css') }}">

    <!-- Scripts -->
    <script>
        window.userId = {{ auth()->id() }};
    </script>

    <script type="text/javascript" src="{{ asset('trix/trix.js') }}"></script>
</head>
<body class="h-screen antialiased leading-none font-sans flex flex-col main-page theme-light">
<div id="app">
    <top-bar-component></top-bar-component>
    <div class="flex flex-row justify-between" style="margin-top:80px">
        <menu-component tag="{{ $tag ?? '{}' }}"
                        tags="{{ auth()->user()->tags }}"
                        current_route="{{ Route::currentRouteName() }}">

        </menu-component>

        <main class="flex-grow p-10">
            <search-controls-component></search-controls-component>
            @section('content')

            @show
        </main>
    </div>

    <notification-component></notification-component>
    <edit-note-component></edit-note-component>
    <image-viewer-component></image-viewer-component>
</div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
