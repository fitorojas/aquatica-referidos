@extends('layouts.admin')

@section('page_title', 'Configuración del Sistema & Evolution API')

@section('content')
<div class="max-w-4xl space-y-8">
    <!-- Formulario de Configuración General -->
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Bloque 1: Parámetros Comerciales -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-slate-900">Reglas Comerciales del Beneficio</h2>
                <p class="text-xs text-slate-500 mt-1">Configuración del beneficio monetario y vigencia de expedientes.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Monto de Mensualidad Promocional (₡)</label>
                    <input type="number" step="0.01" name="referral_benefit_amount" value="{{ $settings['referral_benefit_amount'] ?? '30800.00' }}" required
                        class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border focus:ring-sky-500 focus:border-sky-500">
                    <span class="text-[11px] text-slate-400 mt-1 block">Aplica para referente y referido.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Prefijo de Código de Expediente</label>
                    <input type="text" name="referral_code_prefix" value="{{ $settings['referral_code_prefix'] ?? 'AQ-REF-' }}" required
                        class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border focus:ring-sky-500 focus:border-sky-500">
                    <span class="text-[11px] text-slate-400 mt-1 block">Ej. AQ-REF-</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Vigencia del Expediente (Días)</label>
                    <input type="number" name="referral_expiration_days" value="{{ $settings['referral_expiration_days'] ?? '60' }}" required
                        class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border focus:ring-sky-500 focus:border-sky-500">
                    <span class="text-[11px] text-slate-400 mt-1 block">Plazo para concretar matrícula.</span>
                </div>
            </div>
        </div>

        <!-- Bloque 2: Integración Evolution API (WhatsApp) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-slate-900">Integración con Evolution API (WhatsApp Gateway)</h2>
                <p class="text-xs text-slate-500 mt-1">Conecta la pasarela de WhatsApp para despachar notificaciones automáticas y alertas grupales.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">URL de Evolution API</label>
                    <input type="url" name="evolution_api_url" value="{{ $settings['evolution_api_url'] ?? '' }}" placeholder="https://tu-evolution-api.com"
                        class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">API Key / Token de Acceso</label>
                    <input type="password" name="evolution_api_key" value="{{ $settings['evolution_api_key'] ?? '' }}" placeholder="••••••••••••••••"
                        class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Nombre de la Instancia Activa</label>
                    <input type="text" name="evolution_instance_name" value="{{ $settings['evolution_instance_name'] ?? 'aquatica_referidos' }}"
                        class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">JID del Grupo Administrativo de WhatsApp</label>
                    <input type="text" name="evolution_admin_group_jid" value="{{ $settings['evolution_admin_group_jid'] ?? '' }}" placeholder="1203630XXXXXXXXX@g.us"
                        class="w-full text-sm rounded-xl border-slate-300 py-2.5 px-3.5 border focus:ring-sky-500 focus:border-sky-500">
                    <span class="text-[11px] text-slate-400 mt-1 block">Recibe alerta inmediata con cada nuevo referido registrado.</span>
                </div>
            </div>

            <!-- Toggles de Notificaciones -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="evolution_notify_admin_group" value="1" {{ !empty($settings['evolution_notify_admin_group']) && $settings['evolution_notify_admin_group'] !== 'false' ? 'checked' : '' }}
                        class="h-4 w-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                    <span class="text-xs font-bold text-slate-700">Enviar alerta automática al grupo administrativo de WhatsApp al registrarse un referido</span>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="evolution_auto_notify_referee" value="1" {{ !empty($settings['evolution_auto_notify_referee']) && $settings['evolution_auto_notify_referee'] !== 'false' ? 'checked' : '' }}
                        class="h-4 w-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                    <span class="text-xs font-bold text-slate-700">Enviar mensaje de bienvenida y confirmación de beneficio de ₡30.800 al referido</span>
                </label>
            </div>

            <div>
                <button type="submit" class="py-3 px-6 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm transition shadow-sm">
                    Guardar Parámetros de Configuración
                </button>
            </div>
        </div>
    </form>

    <!-- Prueba de Conexión Evolution API -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-2">Prueba de Conectividad WhatsApp</h2>
        <p class="text-xs text-slate-500 mb-4">Envía un mensaje de prueba al grupo administrativo configurado para verificar que Evolution API esté respondiendo adecuadamente.</p>

        <form action="{{ route('admin.settings.test_evolution') }}" method="POST">
            @csrf
            <button type="submit" class="py-2.5 px-5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition">
                Enviar Mensaje de Prueba al Grupo Administrativo
            </button>
        </form>
    </div>
</div>
@endsection
