<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="{{ asset('logo-itda.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-amber-50 via-orange-50 to-slate-100">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-amber-400 via-orange-400 to-rose-400"></div>
                <div class="px-8 py-7">
                    <div class="flex flex-col items-center mb-6">
                        <a href="/" class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center p-2 shadow-md ring-2 ring-amber-400/40">
                            <x-application-logo class="w-full h-full fill-current text-gray-500" />
                        </a>
                        <h1 class="mt-4 text-2xl font-bold text-slate-800 tracking-tight">SIMALAB</h1>
                        <p class="text-[11px] uppercase tracking-widest text-amber-600 font-semibold mt-1.5">Dirgantara Adisutjipto</p>
                        <div class="w-10 h-0.5 bg-amber-400 rounded-full mt-3"></div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
            <p class="text-center text-xs text-slate-500 mt-4">© {{ date('Y') }} SIMALAB · Institut Teknologi Dirgantara Adisutjipto</p>
        </div>
    </body>
</html>
