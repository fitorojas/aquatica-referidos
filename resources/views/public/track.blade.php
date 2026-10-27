@extends('layouts.public')

@section('title', 'Consultar Estado de Referido | AQUATICA')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
            Consulta el Estado de tu Referido
        </h1>
        <p class="mt-2 text-sm text-slate-500">
            Ingresa tu código de expediente (ej. <span class="font-mono text-sky-600 font-bold">AQ-REF-XXXXX</span>) o el número de teléfono del referido.
        </p>
    </div>

    <!-- Buscador -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-8">
        <form action="{{ route('public.referral.track') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="q" value="{{ $search }}" required
                placeholder="Código de referido o teléfono (ej. 88888888)"
                class="flex-grow rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-3 px-4 border">
            <button type="submit" class="py-3 px-6 rounded-xl bg-sky-600 text-white font-bold text-sm hover:bg-sky-700 transition shadow-sm">
                Buscar Expediente
            </button>
        </form>
    </div>

    @if ($search && !$referral)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center text-amber-800">
            <p class="font-bold">No se encontró ningún expediente con el término "{{ $search }}".</p>
            <p class="text-xs text-amber-600 mt-1">Verifica el código o intenta con el teléfono registrado.</p>
        </div>
    @elseif ($referral)
        <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
                <div>
                    <span class="text-xs uppercase font-bold text-slate-400">Expediente</span>
                    <h2 class="text-2xl font-mono font-extrabold text-sky-600">{{ $referral->referral_code }}</h2>
                </div>
                <div>
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $referral->status_badge_class }}">
                        {{ $referral->status_label }}
                    </span>
                </div>
            </div>

            <!-- Datos Generales -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 py-6 text-sm border-b border-slate-100">
                <div>
                    <span class="text-xs text-slate-400 block font-medium">Cliente Referente</span>
                    <span class="font-bold text-slate-800">{{ $referral->referrer->full_name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-medium">Contacto Referido</span>
                    <span class="font-bold text-slate-800">{{ $referral->referee->full_name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-medium">Servicio Seleccionado</span>
                    <span class="font-bold text-sky-700">{{ $referral->service->name }}</span>
                </div>
            </div>

            <!-- Línea de Tiempo de Estado -->
            <div class="pt-6">
                <h3 class="text-xs uppercase tracking-wider font-bold text-slate-400 mb-6">Etapas de Validación Comercial</h3>

                @php
                    $steps = [
                        ['id' => 'pending', 'label' => 'Registro Inicial', 'desc' => 'Recibido en el sistema web.'],
                        ['id' => 'in_contact', 'label' => 'Contacto Comercial', 'desc' => 'En coordinación de horario y prueba.'],
                        ['id' => 'eligible', 'label' => 'Elegibilidad Aprobada', 'desc' => 'Validación de nuevo ingreso.'],
                        ['id' => 'enrolled', 'label' => 'Matrícula Realizada', 'desc' => 'Inscrito formalmente en AQUATICA.'],
                        ['id' => 'paid', 'label' => 'Pago Comprobado', 'desc' => 'Comprobante verificado por administración.'],
                        ['id' => 'benefit_approved', 'label' => 'Beneficio ₡30.800', 'desc' => 'Descuento aplicado a ambas partes.'],
                    ];

                    $statusOrder = [
                        'pending' => 1,
                        'in_contact' => 2,
                        'eligible' => 3,
                        'enrolled' => 4,
                        'paid' => 5,
                        'benefit_approved' => 6,
                        'rejected' => -1,
                        'expired' => -1,
                    ];

                    $currentStepLevel = $statusOrder[$referral->status] ?? 1;
                @endphp

                @if ($referral->status === 'rejected')
                    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-rose-800">
                        <h4 class="font-bold text-sm">Expediente no elegible</h4>
                        <p class="text-xs mt-1">Este expediente ha sido evaluado como no aplicable por administración (ej. alumno activo previamente o duplicado). Para consultas adicionales contacta a recepción.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($steps as $idx => $step)
                            @php
                                $stepLevel = $idx + 1;
                                $isCompleted = $currentStepLevel >= $stepLevel;
                                $isCurrent = $currentStepLevel === $stepLevel;
                            @endphp
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mt-0.5 {{ $isCompleted ? 'bg-sky-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                    @if ($isCompleted && !$isCurrent)
                                        ✓
                                    @else
                                        {{ $stepLevel }}
                                    @endif
                                </div>
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold {{ $isCurrent ? 'text-sky-700' : ($isCompleted ? 'text-slate-800' : 'text-slate-400') }}">
                                            {{ $step['label'] }}
                                        </span>
                                        @if ($isCurrent)
                                            <span class="text-xs bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full font-bold">Paso actual</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500">{{ $step['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
