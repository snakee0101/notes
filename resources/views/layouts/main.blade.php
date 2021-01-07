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
</head>
<body class="bg-gray-100 h-screen antialiased leading-none font-sans flex flex-col">
    <header class="flex flex-row justify-between mb-3 shadow-sm">
        <section>
            keep
        </section>

        <section>
            Search
        </section>

        <section>
            Menu
        </section>
    </header>
    <div class="flex flex-row justify-between">
        <nav class="flex flex-col" style="width: 280px">
            <a href="{{ route('notes') }}" class="bg-yellow-100 p-4 rounded-r-full">Notes</a>
            <a href="{{ route('reminders') }}" class="p-4 rounded-r-full hover:bg-gray-200">Reminders</a>
            <a href="{{ route('tag', 'tag 1') }}" class="p-4 rounded-r-full hover:bg-gray-200">Tag 1</a>
            <a href="{{ route('tag', 'tag 2') }}" class="p-4 rounded-r-full hover:bg-gray-200">Tag 2</a>
            <a href="" class="p-4 rounded-r-full hover:bg-gray-200">Edit labels</a>
            <a href="{{ route('archive') }}" class="p-4 rounded-r-full hover:bg-gray-200">Archive</a>
            <a href="{{ route('trash') }}" class="p-4 rounded-r-full hover:bg-gray-200">Trash</a>
        </nav>
        <main class="bg-green-400 flex-grow" id="app">
            @section('content')

            @show
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
