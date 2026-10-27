<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Administrativo | AQUATICA Referidos')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Assets Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-800 bg-slate-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Izquierda -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 border-r border-slate-800">
            <!-- Brand Header -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800 bg-slate-950/40">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500 to-cyan-400 flex items-center justify-center text-white font-extrabold shadow-md shadow-sky-500/20 mr-3">
                    AQ
                </div>
                <div>
                    <span class="text-lg font-extrabold text-white tracking-wider block">AQUATICA</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-sky-400">Panel Administrativo</span>
                </div>
            </div>

            <!-- Menú de Navegación -->
            <nav class="flex-grow px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.referrals.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.referrals.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Expedientes (CRM)
                </a>

                <a href="{{ route('admin.benefits.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.benefits.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Historial Beneficios
                </a>

                <a href="{{ route('admin.duplicates.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.duplicates.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Auditoría Duplicados
                </a>

                <div class="pt-4 pb-2">
                    <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Configuración</span>
                </div>

                <a href="{{ route('admin.settings.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.settings.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Ajustes & Evolution API
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-sky-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Gestión de Usuarios
                </a>
            </nav>

            <!-- Footer Usuario -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/30">
                <div class="flex items-center justify-between mb-3">
                    <div class="overflow-hidden mr-2">
                        <span class="block text-xs font-bold text-white truncate">{{ auth()->user()->name }}</span>
                        <span class="block text-[11px] text-slate-400 uppercase tracking-wider">{{ auth()->user()->role }}</span>
                    </div>
                    <a href="{{ route('public.referral.create') }}" target="_blank" title="Ver Formulario Web" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-3 py-2 rounded-xl bg-slate-800 hover:bg-rose-900/40 text-slate-300 hover:text-rose-300 text-xs font-bold transition">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenedor Principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Barra Superior -->
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 flex-shrink-0">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-extrabold text-slate-900">@yield('page_title', 'Administración')</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('public.referral.create') }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold bg-sky-50 text-sky-700 hover:bg-sky-100 transition border border-sky-200">
                        Abrir Formulario Web ↗
                    </a>
                </div>
            </header>

            <!-- Mensajes Flash -->
            @if (session('status'))
                <div class="bg-emerald-500 text-white px-8 py-2.5 text-sm font-semibold flex items-center justify-between shadow-sm">
                    <span>{{ session('status') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-100 hover:text-white">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-500 text-white px-8 py-2.5 text-sm font-semibold flex items-center justify-between shadow-sm">
                    <span>{{ $errors->first() }}</span>
                    <button onclick="this.parentElement.remove()" class="text-rose-100 hover:text-white">&times;</button>
                </div>
            @endif

            <!-- Área de Contenido con Scroll -->
            <main class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
