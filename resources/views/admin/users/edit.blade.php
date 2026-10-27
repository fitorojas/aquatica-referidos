@extends('layouts.admin')

@section('page_title', 'Editar Usuario: ' . $user->name)

@section('content')
<div class="max-w-2xl bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
    <h2 class="text-lg font-bold text-slate-900 mb-6">Modificar Usuario</h2>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nombre Completo *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Correo Electrónico *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nueva Contraseña <span class="text-xs text-slate-400 font-normal">(Dejar en blanco para mantener actual)</span></label>
            <input type="password" name="password" minlength="8" class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Rol de Acceso *</label>
            <select name="role" required class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border bg-white">
                <option value="receptionist" {{ $user->role === 'receptionist' ? 'selected' : '' }}>Recepción</option>
                <option value="commercial" {{ $user->role === 'commercial' ? 'selected' : '' }}>Comercial</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Administrador</option>
            </select>
        </div>

        <div>
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="h-4 w-4 text-sky-600 rounded border-slate-300">
                <span class="text-xs font-bold text-slate-700">Usuario Activo</span>
            </label>
        </div>

        <div class="pt-2 flex items-center justify-between">
            <button type="submit" class="py-3 px-6 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm transition">
                Actualizar Usuario
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-500 hover:underline">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
