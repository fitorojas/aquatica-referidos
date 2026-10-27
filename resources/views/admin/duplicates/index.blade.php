@extends('layouts.admin')

@section('page_title', 'Auditoría de Duplicados & Coincidencias')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Bandeja de Detección de Duplicados</h2>
            <p class="text-xs text-slate-500 mt-1">
                El sistema detecta automáticamente coincidencias por número telefónico, cédula, correo o similitud en el nombre del alumno para prevenir fraudes.
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.duplicates.index', ['status' => 'unresolved']) }}"
                class="py-1.5 px-3 rounded-xl text-xs font-bold transition {{ $status === 'unresolved' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 text-slate-600' }}">
                Pendientes
            </a>
            <a href="{{ route('admin.duplicates.index', ['status' => 'resolved']) }}"
                class="py-1.5 px-3 rounded-xl text-xs font-bold transition {{ $status === 'resolved' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                Resueltos
            </a>
            <a href="{{ route('admin.duplicates.index', ['status' => 'all']) }}"
                class="py-1.5 px-3 rounded-xl text-xs font-bold transition {{ $status === 'all' ? 'bg-sky-100 text-sky-900 border border-sky-300' : 'bg-slate-100 text-slate-600' }}">
                Todos
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Expediente</th>
                        <th class="py-3 px-6">Criterio Coincidente</th>
                        <th class="py-3 px-6">Valor Evaluado</th>
                        <th class="py-3 px-6">Confianza</th>
                        <th class="py-3 px-6">Observación / Motivo</th>
                        <th class="py-3 px-6">Estado</th>
                        <th class="py-3 px-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($detections as $det)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 font-mono font-bold text-sky-700">
                                <a href="{{ route('admin.referrals.show', $det->referral_id) }}" class="hover:underline">
                                    {{ $det->referral->referral_code }}
                                </a>
                            </td>
                            <td class="py-4 px-6 font-bold text-xs uppercase text-slate-700">
                                {{ $det->matched_field }}
                            </td>
                            <td class="py-4 px-6 font-mono text-xs text-slate-800">
                                {{ $det->matched_value }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-xs font-extrabold px-2 py-0.5 rounded-full {{ $det->match_confidence >= 90 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $det->match_confidence }}%
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                {{ $det->resolution_notes }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $det->is_resolved ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $det->is_resolved ? 'Resuelto' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if (!$det->is_resolved)
                                    <form action="{{ route('admin.duplicates.resolve', $det) }}" method="POST" class="inline-flex gap-2">
                                        @csrf
                                        <input type="hidden" name="resolution_notes" value="Verificado manualmente por recepción. No representa duplicidad indebida.">
                                        <button type="submit" class="py-1 px-3 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs transition">
                                            Aprobar / Resolver
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Archivado</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">No hay alertas de duplicados registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($detections->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $detections->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
