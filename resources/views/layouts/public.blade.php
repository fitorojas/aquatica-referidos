<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Programa de Referidos | AQUATICA')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Assets Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans antialiased text-slate-800 bg-slate-50">
    <!-- Navbar Superior -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logotipo AQUATICA -->
                <a href="{{ route('public.referral.create') }}" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-sky-600 to-cyan-400 flex items-center justify-center text-white shadow-md shadow-sky-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-900">AQUATICA</span>
                        <span class="block text-xs font-semibold uppercase tracking-wider text-sky-600">Programa de Referidos</span>
                    </div>
                </a>

                <!-- Navegación -->
                <nav class="flex items-center space-x-2 sm:space-x-4">
                    <a href="{{ route('public.referral.create') }}" class="px-3 py-2 text-sm font-semibold rounded-lg text-slate-700 hover:text-sky-600 hover:bg-slate-100 transition">
                        Registrar Referido
                    </a>
                    <a href="{{ route('public.referral.track') }}" class="px-3 py-2 text-sm font-semibold rounded-lg text-slate-700 hover:text-sky-600 hover:bg-slate-100 transition">
                        Consultar Estado
                    </a>
                    <a href="{{ route('admin.login') }}" class="px-3 py-2 text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition border border-slate-200">
                        Acceso Admin
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Notificaciones Flash -->
    @if (session('status'))
        <div class="bg-emerald-500 text-white px-4 py-3 shadow-sm">
            <div class="max-w-7xl mx-auto flex items-center space-x-3">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <!-- Contenido Principal -->
    <main class="flex-grow py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <span class="text-xl font-extrabold text-white tracking-wider">AQUATICA</span>
                    <p class="mt-2 text-sm text-slate-400">
                        Academia y centro de natación de excelencia. Formando nadadores seguros, saludables y apasionados por el agua.
                    </p>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-3">Servicios Participantes</h3>
                    <ul class="text-sm space-y-1.5 text-slate-400">
                        <li>• Bebés y Niños Principiantes</li>
                        <li>• Niños Grandes y Adolescentes</li>
                        <li>• Adultos Principiantes</li>
                        <li>• Open Pool</li>
                        <li>• Aquafitness</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-3">Términos del Beneficio</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Beneficio comercial de mensualidad promocional de ₡30.800 aplicable tanto al cliente referente como al referido una vez validada la matrícula y el pago correspondiente por la administración de AQUATICA. Sujeto al reglamento oficial.
                    </p>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} AQUATICA. Todos los derechos reservados. Módulo de Referidos autónomo v1.0.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
