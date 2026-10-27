@extends('layouts.public')

@section('title', '¡Referencia Registrada con Éxito! | AQUATICA')

@section('content')
<div class="max-w-2xl mx-auto text-center py-6">
    <!-- Icono Check de Éxito -->
    <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center mb-6 shadow-inner">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
        </svg>
    </div>

    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 mb-3">
        Registro Completado
    </span>
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">
        ¡Referencia recibida con éxito!
    </h1>
    <p class="mt-3 text-base text-slate-600">
        Hemos registrado tu recomendación para el programa de natación de AQUATICA.
    </p>

    <!-- Tarjeta del Código de Referido -->
    <div class="mt-8 bg-white rounded-3xl p-8 border border-slate-200 shadow-sm text-left">
        <div class="text-center pb-6 border-b border-slate-100">
            <span class="text-xs uppercase tracking-wider font-bold text-slate-400">Código de Expediente</span>
            <div class="mt-1 font-mono text-3xl font-extrabold text-sky-600 tracking-wider">
                {{ $referral->referral_code }}
            </div>
            <p class="mt-1 text-xs text-slate-500">Guarda este código para consultar el avance de tu beneficio.</p>
        </div>

        <div class="py-6 space-y-3 text-sm border-b border-slate-100">
            <div class="flex justify-between">
                <span class="text-slate-500">Cliente Referente:</span>
                <span class="font-bold text-slate-800">{{ $referral->referrer->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Contacto Referido:</span>
                <span class="font-bold text-slate-800">{{ $referral->referee->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Servicio solicitado:</span>
                <span class="font-bold text-sky-700">{{ $referral->service->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Beneficio acordado:</span>
                <span class="font-extrabold text-emerald-600">₡{{ number_format($benefitAmount, 0, ',', '.') }} para ambos</span>
            </div>
        </div>

        <!-- Botón para compartir por WhatsApp -->
        @php
            $waMessage = rawurlencode("¡Hola {$referral->referee->full_name}! Te he referido a la academia AQUATICA para el servicio {$referral->service->name}. Tu código de referencia es {$referral->referral_code}. Puedes consultar el estado y coordinar tu lección de prueba. ¡Bienvenido!");
            $waUrl = "https://wa.me/{$referral->referee->normalized_phone}?text={$waMessage}";
        @endphp

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ $waUrl }}" target="_blank" class="flex-1 inline-flex items-center justify-center py-3 px-4 rounded-xl bg-emerald-600 text-white font-bold text-sm hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                </svg>
                Avisar a {{ explode(' ', $referral->referee->full_name)[0] }} por WhatsApp
            </a>
            <a href="{{ route('public.referral.track') }}?q={{ $referral->referral_code }}" class="inline-flex items-center justify-center py-3 px-4 rounded-xl bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition border border-slate-200">
                Ver estado del expediente
            </a>
        </div>
    </div>

    <!-- Pasos para activar el beneficio -->
    <div class="mt-12 text-left">
        <h2 class="text-lg font-bold text-slate-900 mb-6 text-center">¿Cuáles son los siguientes pasos?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-5 rounded-2xl border border-slate-200">
                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm mb-3">1</div>
                <h3 class="font-bold text-slate-900 text-sm">Contacto Inicial</h3>
                <p class="text-xs text-slate-500 mt-1">Recepción contactará a tu referido para ofrecerle opciones de horario y evaluación acuática.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200">
                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm mb-3">2</div>
                <h3 class="font-bold text-slate-900 text-sm">Matrícula y Pago</h3>
                <p class="text-xs text-slate-500 mt-1">Tu referido formaliza su inscripción en cualquiera de los 5 servicios autorizados y realiza su pago.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mb-3">3</div>
                <h3 class="font-bold text-slate-900 text-sm">₡30.800 para Ambos</h3>
                <p class="text-xs text-slate-500 mt-1">La administración verifica el pago y aplica la mensualidad de ₡30.800 a ambas cuentas.</p>
            </div>
        </div>
    </div>
</div>
@endsection
