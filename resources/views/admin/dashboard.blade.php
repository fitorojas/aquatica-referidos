@extends('layouts.admin')

@section('page_title', 'Dashboard de Referidos')

@section('content')
<div class="space-y-8">
    <!-- Grid de KPIs Principales -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Referidos -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Referidos</span>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalReferrals) }}</div>
                <span class="text-xs text-sky-600 font-semibold mt-1 block">Expedientes registrados</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <!-- Card 2: En Proceso / Contacto -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">En Gestión Comercial</span>
                <div class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($pendingCount) }}</div>
                <span class="text-xs text-slate-500 font-semibold mt-1 block">Nuevos o en contacto</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Card 3: Matriculados / Beneficios -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Beneficios Aprobados</span>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($benefitsApprovedCount) }}</div>
                <span class="text-xs text-emerald-600 font-semibold mt-1 block">Tarifa ₡30.800 activa</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Card 4: Tasa de Conversión -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tasa de Conversión</span>
                <div class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $conversionRate }}%</div>
                <span class="text-xs text-slate-500 font-semibold mt-1 block">{{ $enrolledCount }} matriculados</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Fila Media: Servicios y Alertas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Desglose por Servicio -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-6">Demanda por Servicio Participante</h2>
            <div class="space-y-4">
                @foreach ($servicesStats as $serv)
                    @php
                        $pct = $totalReferrals > 0 ? round(($serv->referrals_count / $totalReferrals) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1">
                            <span class="text-slate-800">{{ $serv->name }}</span>
                            <span class="text-slate-500">{{ $serv->referrals_count }} referidos ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-sky-500 h-2.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Alertas y Resumen de Descuentos -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 mb-4">Control Financiero & Auditoría</h2>
                
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 mb-4">
                    <span class="text-xs text-slate-500 font-medium block">Total Beneficios Otorgados (₡30.800 c/u)</span>
                    <span class="text-2xl font-extrabold text-slate-900 mt-1 block">₡{{ number_format($benefitsTotalAmount, 0, ',', '.') }}</span>
                </div>

                <div class="p-4 rounded-xl {{ $duplicateAlertsCount > 0 ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-emerald-50 border-emerald-200 text-emerald-900' }} border">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider">Alertas de Duplicados</span>
                        <span class="font-extrabold text-base">{{ $duplicateAlertsCount }}</span>
                    </div>
                    <p class="text-xs mt-1 {{ $duplicateAlertsCount > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                        {{ $duplicateAlertsCount > 0 ? 'Hay coincidencias pendientes de revisión manual.' : 'No hay alertas pendientes de resolver.' }}
                    </p>
                    @if ($duplicateAlertsCount > 0)
                        <a href="{{ route('admin.duplicates.index') }}" class="mt-2 inline-block text-xs font-bold text-amber-800 underline">
                            Revisar duplicados →
                        </a>
                    @endif
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <a href="{{ route('admin.exports.referrals') }}" class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exportar Todo a CSV / Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Tabla: Últimos Referidos Registrados -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Últimos Referidos Registrados</h2>
            <a href="{{ route('admin.referrals.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700">
                Ver todos los expedientes →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Código</th>
                        <th class="py-3 px-6">Referente</th>
                        <th class="py-3 px-6">Contacto Referido</th>
                        <th class="py-3 px-6">Servicio</th>
                        <th class="py-3 px-6">Estado</th>
                        <th class="py-3 px-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentReferrals as $ref)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 font-mono font-bold text-sky-700">
                                {{ $ref->referral_code }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-800 block">{{ $ref->referrer->full_name }}</span>
                                <span class="text-xs text-slate-400">{{ $ref->referrer->phone }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-800 block">{{ $ref->referee->full_name }}</span>
                                <span class="text-xs text-slate-500">{{ $ref->referee->phone }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600 font-medium">
                                {{ $ref->service->name }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $ref->status_badge_class }}">
                                    {{ $ref->status_label }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.referrals.show', $ref) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 font-bold text-xs transition border border-sky-200">
                                    Ver Expediente
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No hay referidos registrados aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
