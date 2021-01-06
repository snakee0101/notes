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
<body class="bg-gray-100 h-screen antialiased leading-none font-sans m-2 flex flex-col">
    <header class="flex flex-row justify-between">
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
        <nav class="bg-blue-400" style="width: 280px">
            75207520
        </nav>
        <main class="bg-green-400 flex-grow">
            75207520
        </main>
    </div>
</body>
</html>
