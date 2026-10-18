<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Quản lý sinh viên') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @stack('head')
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen flex-col lg:flex-row">
        @include('partials.sidebar')

        <div class="flex min-w-0 flex-1 flex-col">
            @include('partials.topbar')

            <main class="flex-1 p-4 lg:p-6">
                @include('partials.flash')
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white px-4 py-3 text-center text-xs text-slate-500 lg:px-6">
                Hệ thống quản lý sinh viên · Laravel {{ app()->version() }}
            </footer>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
