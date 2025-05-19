<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">
    <header class="bg-white shadow p-4 mb-6">
        <div class="container mx-auto">
            <h1 class="text-xl font-bold">{{ config('app.name') }}</h1>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>
