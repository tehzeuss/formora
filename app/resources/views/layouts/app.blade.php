<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Formora: crie formulários, compartilhe ideias e acompanhe respostas.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/formora.svg') }}">
    <title>@yield('title', 'Formora')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="@guest guest-layout @endguest">
    @include('partials.header')
    @include('partials.main')
    @include('partials.footer')
    @stack('scripts')
</body>
</html>
