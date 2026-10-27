@extends('layouts.admin')

@section('page_title', 'Crear Usuario Administrativo')

@section('content')
<div class="max-w-2xl bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
    <h2 class="text-lg font-bold text-slate-900 mb-6">Nuevo Usuario del Panel</h2>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nombre Completo *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Correo Electrónico *</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Contraseña *</label>
            <input type="password" name="password" required minlength="8" class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Rol de Acceso *</label>
            <select name="role" required class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border bg-white">
                <option value="receptionist">Recepción (Gestión y Contacto)</option>
                <option value="commercial">Comercial / Asesor de Ventas</option>
                <option value="admin">Administrador (Verificaciones y Aprobaciones)</option>
                <option value="super_admin">Super Administrador (Acceso Total)</option>
            </select>
        </div>

        <div class="pt-2">
            <button type="submit" class="py-3 px-6 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm transition">
                Guardar Usuario
            </button>
            <a href="{{ route('admin.users.index') }}" class="ml-2 text-xs font-bold text-slate-500 hover:underline">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
