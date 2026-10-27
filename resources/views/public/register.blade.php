@extends('layouts.public')

@section('title', 'Registro de Referido | AQUATICA')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-600 via-sky-700 to-cyan-600 text-white p-8 sm:p-12 mb-10 shadow-xl shadow-sky-600/20">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-white backdrop-blur mb-4">
                Beneficio Exclusivo Doble Vía
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                ¡Ambos disfrutan de una mensualidad de ₡30.800!
            </h1>
            <p class="mt-3 text-base sm:text-lg text-sky-100 leading-relaxed">
                Refiere a un amigo, familiar o conocido a AQUATICA. Cuando tu referido complete su matrícula en uno de nuestros 5 servicios participantes, <strong class="text-white underline">ambos recibirán su mensualidad a tarifa preferencial de ₡{{ number_format($benefitAmount, 0, ',', '.') }}</strong> (precio regular <span class="line-through">₡35.000</span>).
            </p>
        </div>
        <!-- Decoración de fondo -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- Errores de Validación Globales -->
    @if ($errors->any())
        <div class="mb-8 rounded-2xl bg-rose-50 p-6 border border-rose-200 text-rose-800">
            <div class="flex items-start">
                <svg class="w-6 h-6 mr-3 text-rose-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="text-base font-bold">Por favor revisa los siguientes campos:</h3>
                    <ul class="mt-2 list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulario de Registro Simplificado -->
    <form action="{{ route('public.referral.store') }}" method="POST" class="space-y-10">
        @csrf

        <!-- SECCIÓN 1: Datos de quien refiere -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                    1
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">¿Quién refiere? (Tus datos)</h2>
                    <p class="text-xs text-slate-500">Datos del cliente actual de AQUATICA que recibirá el beneficio.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre completo *</label>
                    <input type="text" name="referrer[full_name]" value="{{ old('referrer.full_name') }}" required
                        placeholder="Ej. Carlos Morales Pérez"
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 px-3.5 border">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Teléfono / WhatsApp *</label>
                    <input type="tel" name="referrer[phone]" value="{{ old('referrer.phone') }}" required
                        placeholder="Ej. 8888 8888"
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 px-3.5 border">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cédula o DIMEX <span class="text-xs text-slate-400 font-normal">(Opcional)</span></label>
                    <input type="text" name="referrer[identification]" value="{{ old('referrer.identification') }}"
                        placeholder="Ej. 1-1234-0567"
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 px-3.5 border">
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: Datos de la persona referida / Alumno -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                    2
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">¿A quién refieres? (¿Quién asistirá a las clases? (Datos del Alumno))</h2>
                    <p class="text-xs text-slate-500">Datos de la persona o alumno a quien contactaremos para coordinar su ingreso a la academia.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre del referido / Alumno *</label>
                    <input type="text" name="referee[full_name]" value="{{ old('referee.full_name') }}" required
                        placeholder="Ej. Mariana Solís Rojas"
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 px-3.5 border">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Teléfono / WhatsApp *</label>
                    <input type="tel" name="referee[phone]" value="{{ old('referee.phone') }}" required
                        placeholder="Ej. 8765 4321"
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 px-3.5 border">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cédula o DIMEX <span class="text-xs text-slate-400 font-normal">(Opcional)</span></label>
                    <input type="text" name="referee[identification]" value="{{ old('referee.identification') }}"
                        placeholder="Ej. 1-0987-0654"
                        class="w-full rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 px-3.5 border">
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3: Selección del Servicio Participante -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                    3
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Servicio de Interés</h2>
                    <p class="text-xs text-slate-500">Todos los servicios participantes aplican la mensualidad preferencial de <strong>₡{{ number_format($benefitAmount, 0, ',', '.') }}</strong> en lugar de su tarifa regular de ₡35.000.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($services as $service)
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition hover:border-sky-400 group {{ old('service_id', 1) == $service->id ? 'border-sky-600 bg-sky-50/50' : 'border-slate-200' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 group-hover:text-sky-600 transition">{{ $service->name }}</span>
                            <input type="radio" name="service_id" value="{{ $service->id }}" {{ old('service_id', 1) == $service->id ? 'checked' : '' }} class="h-4 w-4 text-sky-600 border-slate-300 focus:ring-sky-500">
                        </div>
                        <p class="text-xs text-slate-500 mb-3 flex-grow">{{ $service->description }}</p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                            <span class="text-slate-400 line-through">₡35.000</span>
                            <span class="font-extrabold text-sky-700 bg-sky-100 px-2.5 py-0.5 rounded-full">Tarifa Referido: ₡{{ number_format($service->promo_price, 0, ',', '.') }}</span>
                        </div>
                    </label>
                @endforeach
            </div>

            <!-- Notas adicionales -->
            <div class="mt-6">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Comentarios adicionales o preferencias de horario <span class="text-xs text-slate-400 font-normal">(Opcional)</span></label>
                <textarea name="notes" rows="2" placeholder="Ej. Preferencia por horario matutino o fines de semana."
                    class="w-full rounded-xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2.5 px-3.5 border">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Términos y Envío -->
        <div class="bg-slate-100 rounded-2xl p-6 border border-slate-200">
            <label class="flex items-start space-x-3 cursor-pointer">
                <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required
                    class="mt-1 h-4 w-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                <span class="text-xs text-slate-600 leading-relaxed">
                    Acepto los términos y condiciones del programa. Entiendo que la tarifa de <strong>₡{{ number_format($benefitAmount, 0, ',', '.') }}</strong> se activará tanto para mí como para mi referido una vez que la administración de AQUATICA valide la elegibilidad, la matrícula y el pago correspondiente.
                </span>
            </label>

            <div class="mt-6">
                <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-sky-600 to-cyan-600 text-white font-extrabold text-base shadow-lg shadow-sky-600/30 hover:from-sky-700 hover:to-cyan-700 transition transform hover:-translate-y-0.5">
                    Registrar Referido y Solicitar Beneficio de ₡{{ number_format($benefitAmount, 0, ',', '.') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
