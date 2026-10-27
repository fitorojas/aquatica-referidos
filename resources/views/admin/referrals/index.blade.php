@extends('layouts.admin')

@section('page_title', 'Expedientes de Referidos (CRM)')

@section('content')
<div class="space-y-6">
    <!-- Barra de Filtros y Búsqueda -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.referrals.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Búsqueda General -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Buscar</label>
                <input type="text" name="q" value="{{ $search }}" placeholder="Código, nombre o teléfono..."
                    class="w-full rounded-xl border-slate-300 text-xs py-2 px-3 focus:ring-sky-500 focus:border-sky-500 border">
            </div>

            <!-- Filtro Estado -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Estado</label>
                <select name="status" class="w-full rounded-xl border-slate-300 text-xs py-2 px-3 focus:ring-sky-500 focus:border-sky-500 border bg-white">
                    <option value="">Todos los Estados</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Nuevo Registrado</option>
                    <option value="in_contact" {{ $status === 'in_contact' ? 'selected' : '' }}>En Contacto</option>
                    <option value="eligible" {{ $status === 'eligible' ? 'selected' : '' }}>Elegibilidad Aprobada</option>
                    <option value="enrolled" {{ $status === 'enrolled' ? 'selected' : '' }}>Matriculado</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Pago Verificado</option>
                    <option value="benefit_approved" {{ $status === 'benefit_approved' ? 'selected' : '' }}>Beneficio Aprobado (₡30.800)</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rechazado</option>
                </select>
            </div>

            <!-- Filtro Servicio -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Servicio</label>
                <select name="service_id" class="w-full rounded-xl border-slate-300 text-xs py-2 px-3 focus:ring-sky-500 focus:border-sky-500 border bg-white">
                    <option value="">Todos los Servicios</option>
                    @foreach ($services as $serv)
                        <option value="{{ $serv->id }}" {{ $serviceId == $serv->id ? 'selected' : '' }}>{{ $serv->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs transition shadow-sm">
                    Filtrar
                </button>
                <a href="{{ route('admin.referrals.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                    Limpiar
                </a>
                <a href="{{ route('admin.exports.referrals') }}?{{ http_build_query(request()->all()) }}" title="Exportar filtro a CSV" class="py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm">
                    CSV ⬇
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla Principal -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Código / Fecha</th>
                        <th class="py-3 px-6">Referente (Cliente)</th>
                        <th class="py-3 px-6">Referido (Contacto)</th>
                        <th class="py-3 px-6">Alumno a Matricular</th>
                        <th class="py-3 px-6">Servicio</th>
                        <th class="py-3 px-6">Estado</th>
                        <th class="py-3 px-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($referrals as $ref)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6">
                                <span class="font-mono font-bold text-sky-700 block">{{ $ref->referral_code }}</span>
                                <span class="text-xs text-slate-400">{{ $ref->created_at->format('d/m/Y H:i') }}</span>
                                @if ($ref->duplicateDetections->where('is_resolved', false)->count() > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 mt-1">
                                        ⚠ Coincidencia
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-800 block">{{ $ref->referrer->full_name }}</span>
                                <span class="text-xs text-slate-500">{{ $ref->referrer->phone }}</span>
                            </td>

                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-800 block">{{ $ref->referee->full_name }}</span>
                                <span class="text-xs text-slate-500">{{ $ref->referee->phone }}</span>
                            </td>

                            <td class="py-4 px-6">
                                @if ($ref->student && !$ref->student->is_same_as_customer)
                                    <span class="font-semibold text-slate-800 block">{{ $ref->student->full_name }}</span>
                                    <span class="text-[11px] text-slate-400">Menor ({{ $ref->student->age ? $ref->student->age . ' años' : 'Edad N/D' }})</span>
                                @else
                                    <span class="text-xs text-sky-700 font-semibold bg-sky-50 px-2 py-0.5 rounded border border-sky-100">
                                        Mismo Adulto
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <span class="font-medium text-xs text-slate-700 block">{{ $ref->service->name }}</span>
                                <span class="text-[11px] font-bold text-emerald-600">₡30.800</span>
                            </td>

                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $ref->status_badge_class }}">
                                    {{ $ref->status_label }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.referrals.show', $ref) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-sky-600 text-white hover:bg-sky-700 font-bold text-xs transition shadow-sm">
                                    Expediente 360°
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">No se encontraron expedientes con los criterios seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($referrals->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
