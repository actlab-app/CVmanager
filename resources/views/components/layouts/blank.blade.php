<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Çekiliş Çarkı' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1c2c 0%, #4a192c 100%);
            overflow: hidden;
        }
    </style>
</head>

<body class="h-full flex flex-col items-center justify-center text-white overflow-hidden">
    {{ $slot }}
</body>

</html>