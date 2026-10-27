@extends('layouts.admin')

@section('page_title', 'Historial de Beneficios y Descuentos (₡30.800)')

@section('content')
<div class="space-y-6">
    <!-- Resumen de Métricas de Beneficios -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Descuentos Concedidos</span>
            <div class="text-3xl font-extrabold text-emerald-600 mt-1">₡{{ number_format($totalGranted + $totalApplied, 0, ',', '.') }}</div>
            <span class="text-xs text-slate-400 mt-1 block">Monto total autorizado (₡30.800 por beneficiario)</span>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pendientes de Aplicar en Factura</span>
            <div class="text-3xl font-extrabold text-amber-600 mt-1">₡{{ number_format($totalGranted, 0, ',', '.') }}</div>
            <span class="text-xs text-slate-400 mt-1 block">Listos para reflejar en el cobro del mes</span>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Ya Aplicados en Cobro</span>
            <div class="text-3xl font-extrabold text-slate-900 mt-1">₡{{ number_format($totalApplied, 0, ',', '.') }}</div>
            <span class="text-xs text-emerald-600 mt-1 block">Confirmados en facturación</span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.benefits.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div>
                <select name="status" class="rounded-xl border-slate-300 text-xs py-2 px-3 border bg-white">
                    <option value="">Todos los Estados</option>
                    <option value="granted" {{ $status === 'granted' ? 'selected' : '' }}>Concedido (Pendiente Aplicar)</option>
                    <option value="applied" {{ $status === 'applied' ? 'selected' : '' }}>Aplicado en Facturación</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div>
                <select name="type" class="rounded-xl border-slate-300 text-xs py-2 px-3 border bg-white">
                    <option value="">Ambos Beneficiarios</option>
                    <option value="referrer" {{ $type === 'referrer' ? 'selected' : '' }}>Cliente Referente</option>
                    <option value="referee" {{ $type === 'referee' ? 'selected' : '' }}>Cliente Referido</option>
                </select>
            </div>
            <button type="submit" class="py-2 px-4 rounded-xl bg-sky-600 text-white font-bold text-xs hover:bg-sky-700 transition">
                Filtrar
            </button>
            <a href="{{ route('admin.benefits.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition border border-slate-200">
                Limpiar
            </a>
        </form>
    </div>

    <!-- Tabla de Beneficios -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Expediente</th>
                        <th class="py-3 px-6">Beneficiario</th>
                        <th class="py-3 px-6">Tipo</th>
                        <th class="py-3 px-6">Monto</th>
                        <th class="py-3 px-6">Periodo</th>
                        <th class="py-3 px-6">Estado</th>
                        <th class="py-3 px-6">Autorizado Por</th>
                        <th class="py-3 px-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($benefits as $b)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 font-mono font-bold text-sky-700">
                                <a href="{{ route('admin.referrals.show', $b->referral_id) }}" class="hover:underline">
                                    {{ $b->referral->referral_code }}
                                </a>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-800 block">{{ $b->beneficiary->full_name }}</span>
                                <span class="text-xs text-slate-400">{{ $b->beneficiary->phone }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $b->beneficiary_type === 'referrer' ? 'bg-indigo-50 text-indigo-700' : 'bg-cyan-50 text-cyan-700' }}">
                                    {{ $b->beneficiary_type_label }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-extrabold text-emerald-600">
                                ₡{{ number_format($b->discount_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 font-mono text-xs text-slate-600">
                                {{ $b->apply_to_period ?? 'N/D' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $b->status === 'applied' ? 'bg-emerald-100 text-emerald-800' : ($b->status === 'granted' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ ucfirst($b->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $b->authorizer->name ?? 'Sistema' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if ($b->status === 'granted')
                                    <form action="{{ route('admin.benefits.apply', $b) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="py-1 px-3 rounded-lg bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition">
                                            Aplicar Cobro
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Sin acción</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">No hay registros de beneficios aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($benefits->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $benefits->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
