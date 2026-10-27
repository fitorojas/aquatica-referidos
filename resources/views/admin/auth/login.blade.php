<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso Administrativo | AQUATICA Referidos</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-500 to-cyan-400 mx-auto flex items-center justify-center text-white font-extrabold text-2xl shadow-xl shadow-sky-500/20 mb-3">
                AQ
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-wider">AQUATICA</h1>
            <p class="text-xs uppercase tracking-widest text-sky-400 font-bold mt-1">Panel de Referidos & Comercial</p>
        </div>

        <!-- Formulario Login -->
        <div class="bg-white rounded-3xl p-8 shadow-2xl border border-slate-100">
            <h2 class="text-lg font-bold text-slate-900 mb-6 text-center">Iniciar Sesión</h2>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@aquatica.cr') }}" required autofocus
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 px-3.5 border">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Contraseña</label>
                    <input type="password" name="password" required
                        placeholder="••••••••"
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 px-3.5 border">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 cursor-pointer text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span>Recordarme</span>
                    </label>
                </div>

                <div>
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm transition shadow-md shadow-sky-600/20">
                        Acceder al Panel
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="{{ route('public.referral.create') }}" class="text-xs text-sky-600 hover:text-sky-700 font-bold">
                    ← Volver al Formulario de Referidos
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            &copy; {{ date('Y') }} AQUATICA. Acceso exclusivo para personal autorizado.
        </p>
    </div>
</body>
</html>
