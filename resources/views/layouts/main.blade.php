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
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script>
        window.userId = {{ auth()->id() }};
    </script>

    <link rel="stylesheet" type="text/css" href="{{ asset('trix/trix.css') }}">
    <script type="text/javascript" src="{{ asset('trix/trix.js') }}"></script>

    <script async defer src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.0.0/masonry.pkgd.min.js"></script>
</head>
<body class="h-screen antialiased leading-none font-sans flex flex-col">
<div id="app">
    <top-bar-component></top-bar-component>
    <div class="flex flex-row justify-between" style="margin-top:80px">
        <menu-component current_route="{{ Route::currentRouteName() }}"
                        tag_link="{{ $tag_name ?? '' }}"
                        tag_names="{{ \App\Models\Tag::getAllNames() }}">

        </menu-component>

        <main class="flex-grow p-10">
            <search-controls-component></search-controls-component>
            @section('content')

            @show
        </main>
    </div>

    <notification-component></notification-component>
    <edit-note-component></edit-note-component>
</div>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        if(!Notification.permission !== 'granted')
            Notification.requestPermission();

        window.redirectToNote = function(){
            location.hash = window.notificationData.link;
        };

        window.Echo.private('App.Models.User.' + window.userId)
            .notification(function(notification) {
                window.notificationData = notification;
                new Notification(notification.reminder_text).onclick = window.redirectToNote;
            });
    </script>
</body>
</html>
