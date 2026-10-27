@extends('layouts.admin')

@section('page_title', 'Gestión de Usuarios Administrativos')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Usuarios del Panel</h2>
            <p class="text-xs text-slate-500 mt-1">Control de acceso y roles para el personal de AQUATICA.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="py-2 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs transition shadow-sm">
            + Nuevo Usuario
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Nombre</th>
                        <th class="py-3 px-6">Correo</th>
                        <th class="py-3 px-6">Rol</th>
                        <th class="py-3 px-6">Estado</th>
                        <th class="py-3 px-6">Fecha Registro</th>
                        <th class="py-3 px-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $u->name }}</td>
                            <td class="py-4 px-6 text-xs text-slate-600">{{ $u->email }}</td>
                            <td class="py-4 px-6">
                                <span class="text-xs font-bold uppercase px-2 py-0.5 rounded-full {{ $u->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : ($u->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $u->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $u->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-400">{{ $u->created_at->format('d/m/Y') }}</td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.users.edit', $u) }}" class="text-xs font-bold text-sky-600 hover:text-sky-800">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No hay usuarios adicionales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
