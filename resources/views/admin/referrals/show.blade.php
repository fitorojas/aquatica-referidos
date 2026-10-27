@extends('layouts.admin')

@section('page_title', 'Expediente ' . $referral->referral_code)

@section('content')
<div class="space-y-8">
    <!-- Encabezado del Expediente -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-3">
                    <span class="text-xs uppercase font-extrabold text-slate-400">Expediente de Referido</span>
                    <span class="text-xs text-slate-400">• Registrado el {{ $referral->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <h1 class="text-3xl font-mono font-extrabold text-sky-700 mt-1">{{ $referral->referral_code }}</h1>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-extrabold border {{ $referral->status_badge_class }}">
                    {{ $referral->status_label }}
                </span>
            </div>
        </div>

        <!-- Barra de Progreso Secuencial -->
        <div class="pt-6">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-4">Progreso de Verificación & Activación</span>
            @php
                $stages = [
                    'pending' => ['num' => 1, 'label' => 'Registro'],
                    'in_contact' => ['num' => 2, 'label' => 'Contacto'],
                    'eligible' => ['num' => 3, 'label' => 'Elegibilidad'],
                    'enrolled' => ['num' => 4, 'label' => 'Matrícula'],
                    'paid' => ['num' => 5, 'label' => 'Pago'],
                    'benefit_approved' => ['num' => 6, 'label' => '₡30.800 Activo'],
                ];
                $orderMap = ['pending' => 1, 'in_contact' => 2, 'eligible' => 3, 'enrolled' => 4, 'paid' => 5, 'benefit_approved' => 6, 'rejected' => 0];
                $currentOrder = $orderMap[$referral->status] ?? 1;
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-2">
                @foreach ($stages as $key => $st)
                    @php
                        $isPast = $currentOrder >= $st['num'];
                        $isCurrent = $currentOrder === $st['num'];
                    @endphp
                    <div class="p-3 rounded-xl border text-center {{ $isCurrent ? 'bg-sky-50 border-sky-400 text-sky-800 font-bold' : ($isPast ? 'bg-slate-50 border-slate-200 text-slate-700' : 'bg-white border-slate-100 text-slate-300') }}">
                        <span class="block text-xs font-bold">{{ $st['num'] }}. {{ $st['label'] }}</span>
                        @if ($isPast && !$isCurrent)
                            <span class="text-[10px] text-emerald-600 font-extrabold">✓ Completado</span>
                        @elseif ($isCurrent)
                            <span class="text-[10px] text-sky-600 font-extrabold">● Actual</span>
                        @else
                            <span class="text-[10px] text-slate-300">Pendiente</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Contenido en 2 Columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Columna Izquierda: Verificación y Auditoría (2 Cols) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Módulo de Verificaciones Administrativas -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-4">
                    Flujo de Verificación Administrativa Manual
                </h2>

                <!-- PASO 1: ELEGIBILIDAD -->
                <div class="p-5 rounded-2xl border {{ $referral->eligible_at ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-full {{ $referral->eligible_at ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700' }} flex items-center justify-center text-xs font-bold">1</span>
                            <h3 class="font-bold text-slate-900 text-sm">Paso 1: Verificación de Elegibilidad</h3>
                        </div>
                        @if ($referral->eligible_at)
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">Aprobado el {{ $referral->eligible_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Verifica que el alumno referido sea de nuevo ingreso (sin matrícula activa en los últimos meses).</p>

                    @if (!$referral->eligible_at && $referral->status !== 'rejected')
                        <form action="{{ route('admin.verify.eligibility', $referral) }}" method="POST" class="space-y-3 pt-3 border-t border-slate-200">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Resultado de Elegibilidad</label>
                                <select name="result" class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 border bg-white" required>
                                    <option value="approved">Aprobar Elegibilidad (Alumno Nuevo)</option>
                                    <option value="rejected">Rechazar (Alumno ya matriculado o no elegible)</option>
                                    <option value="pending_info">Solicitar más información</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Observaciones</label>
                                <textarea name="observations" rows="2" required placeholder="Indicar comprobación realizada en expedientes..." class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 border"></textarea>
                            </div>
                            <button type="submit" class="py-2 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold transition">
                                Guardar Verificación de Elegibilidad
                            </button>
                        </form>
                    @endif
                </div>

                <!-- PASO 2: MATRÍCULA -->
                <div class="p-5 rounded-2xl border {{ $referral->enrolled_at ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-full {{ $referral->enrolled_at ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700' }} flex items-center justify-center text-xs font-bold">2</span>
                            <h3 class="font-bold text-slate-900 text-sm">Paso 2: Registro de Matrícula</h3>
                        </div>
                        @if ($referral->enrolled_at)
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">Matriculado el {{ $referral->enrolled_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Registra que el alumno ha formalizado su horario e inscripción en <strong>{{ $referral->service->name }}</strong>.</p>

                    @if ($referral->eligible_at && !$referral->enrolled_at && $referral->status !== 'rejected')
                        <form action="{{ route('admin.verify.enrollment', $referral) }}" method="POST" class="space-y-3 pt-3 border-t border-slate-200">
                            @csrf
                            <input type="hidden" name="result" value="approved">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Notas de Matrícula (Horario, Profesor, Carril)</label>
                                <textarea name="observations" rows="2" required placeholder="Ej. Matriculado en turno martes y jueves 4:00 PM con Prof. Esteban." class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 border"></textarea>
                            </div>
                            <button type="submit" class="py-2 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold transition">
                                Confirmar Matrícula Realizada
                            </button>
                        </form>
                    @elseif (!$referral->eligible_at)
                        <span class="text-xs text-slate-400 italic">Requiere aprobar el Paso 1 (Elegibilidad) primero.</span>
                    @endif
                </div>

                <!-- PASO 3: PAGO Y ACTIVACIÓN DE BENEFICIO (₡30.800) -->
                <div class="p-5 rounded-2xl border {{ $referral->benefit_approved_at ? 'bg-green-50 border-green-300' : 'bg-slate-50 border-slate-200' }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-full {{ $referral->benefit_approved_at ? 'bg-green-600 text-white' : 'bg-slate-300 text-slate-700' }} flex items-center justify-center text-xs font-bold">3</span>
                            <h3 class="font-bold text-slate-900 text-sm">Paso 3: Verificación de Pago & Otorgamiento de ₡30.800</h3>
                        </div>
                        @if ($referral->benefit_approved_at)
                            <span class="text-xs font-bold text-green-900 bg-green-200 px-3 py-1 rounded-full border border-green-400">BENEFICIO ₡30.800 ACTIVADO</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Ingresa el comprobante de pago de la matrícula/mensualidad. Al verificar el pago, el sistema otorgará automáticamente la tarifa de ₡30.800 a ambas personas.</p>

                    @if ($referral->enrolled_at && !$referral->benefit_approved_at && $referral->status !== 'rejected')
                        <form action="{{ route('admin.verify.payment', $referral) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-3 border-t border-slate-200">
                            @csrf
                            <input type="hidden" name="result" value="approved">

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">N° Comprobante SINPE / Transferencia *</label>
                                    <input type="text" name="receipt_number" required placeholder="Ej. #29481048" class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 border">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Monto Pagado (₡) *</label>
                                    <input type="number" step="0.01" name="payment_amount" value="30800.00" required class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 border">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Periodo a aplicar (YYYY-MM)</label>
                                    <input type="text" name="apply_period" value="{{ date('Y-m') }}" placeholder="2026-11" class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 border">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Adjuntar Comprobante (JPG, PNG, PDF)</label>
                                <input type="file" name="proof_document" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Observaciones de Validación Financiera</label>
                                <textarea name="observations" rows="2" required placeholder="Confirmado en cuenta BAC / SINPE Móvil..." class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 border"></textarea>
                            </div>

                            <button type="submit" class="py-2.5 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-md shadow-emerald-600/20">
                                Validar Pago y Otorgar Mensualidad de ₡30.800 a Ambas Partes
                            </button>
                        </form>
                    @elseif (!$referral->enrolled_at)
                        <span class="text-xs text-slate-400 italic">Requiere registrar el Paso 2 (Matrícula) primero.</span>
                    @endif
                </div>
            </div>

            <!-- Beneficios Generados (₡30.800 para Referente y Referido) -->
            @if ($referral->benefits->count() > 0)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-4">
                        Beneficios Comerciales Asignados
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($referral->benefits as $benefit)
                            <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">
                                        {{ $benefit->beneficiary_type_label }}
                                    </span>
                                    <span class="text-xs font-extrabold px-2 py-0.5 rounded-full {{ $benefit->status === 'applied' ? 'bg-emerald-600 text-white' : 'bg-amber-100 text-amber-800' }}">
                                        {{ ucfirst($benefit->status) }}
                                    </span>
                                </div>
                                <div class="text-2xl font-extrabold text-emerald-700">
                                    ₡{{ number_format($benefit->discount_amount, 0, ',', '.') }}
                                </div>
                                <p class="text-xs text-slate-700 font-semibold mt-1">{{ $benefit->beneficiary->full_name }}</p>
                                <p class="text-[11px] text-slate-500 mt-2">Periodo: {{ $benefit->apply_to_period ?? 'N/D' }}</p>

                                @if ($benefit->status === 'granted')
                                    <form action="{{ route('admin.benefits.apply', $benefit) }}" method="POST" class="mt-3 pt-3 border-t border-emerald-200">
                                        @csrf
                                        <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition">
                                            Marcar como Aplicado en Cobro
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Bitácora de Actividades / Seguimiento CRM -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-bold text-slate-900">Bitácora de Seguimiento & Auditoría</h2>
                </div>

                <!-- Formulario Nueva Nota / Llamada -->
                <form action="{{ route('admin.referrals.store_activity', $referral) }}" method="POST" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    @csrf
                    <div class="flex gap-3">
                        <select name="activity_type" class="text-xs rounded-xl border-slate-300 py-1.5 px-3 border bg-white" required>
                            <option value="call">Llamada telefónica</option>
                            <option value="whatsapp_sent">WhatsApp enviado</option>
                            <option value="meeting">Visita presencial</option>
                            <option value="admin_note">Nota interna</option>
                        </select>
                        <input type="text" name="description" placeholder="Escribe el resultado de la gestión o contacto..." required class="flex-grow text-xs rounded-xl border-slate-300 py-1.5 px-3 border">
                        <button type="submit" class="py-1.5 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold transition">
                            Registrar
                        </button>
                    </div>
                </form>

                <!-- Línea de Actividades -->
                <div class="space-y-4">
                    @forelse ($referral->activities as $act)
                        <div class="flex items-start space-x-3 text-xs">
                            <div class="w-2 h-2 rounded-full bg-sky-500 mt-1.5 flex-shrink-0"></div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800">{{ $act->description }}</span>
                                    <span class="text-slate-400">{{ $act->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <span class="text-slate-400 text-[11px]">Por: {{ $act->user->name ?? 'Sistema Automático' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No hay actividades adicionales registradas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Tarjetas de Información 360° (1 Col) -->
        <div class="space-y-6">
            <!-- Tarjeta: Quien Refiere (Cliente) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Cliente Referente</span>
                <h3 class="text-base font-extrabold text-slate-900">{{ $referral->referrer->full_name }}</h3>
                <div class="space-y-1 text-xs text-slate-600">
                    <p><strong>Teléfono:</strong> {{ $referral->referrer->phone }}</p>
                    <p><strong>Cédula:</strong> {{ $referral->referrer->identification ?? 'N/D' }}</p>
                    <p><strong>Email:</strong> {{ $referral->referrer->email ?? 'N/D' }}</p>
                </div>
                <div class="pt-2">
                    <a href="https://wa.me/{{ $referral->referrer->normalized_phone }}" target="_blank" class="w-full inline-flex items-center justify-center py-2 px-3 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs transition border border-emerald-200">
                        Abrir WhatsApp Referente ↗
                    </a>
                </div>
            </div>

            <!-- Tarjeta: Contacto Referido -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Contacto Referido</span>
                <h3 class="text-base font-extrabold text-slate-900">{{ $referral->referee->full_name }}</h3>
                <div class="space-y-1 text-xs text-slate-600">
                    <p><strong>Teléfono:</strong> {{ $referral->referee->phone }}</p>
                    <p><strong>Cédula:</strong> {{ $referral->referee->identification ?? 'N/D' }}</p>
                </div>
                <div class="pt-2">
                    <a href="https://wa.me/{{ $referral->referee->normalized_phone }}" target="_blank" class="w-full inline-flex items-center justify-center py-2 px-3 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 font-bold text-xs transition shadow-sm">
                        Contactar Referido por WhatsApp ↗
                    </a>
                </div>
            </div>

            <!-- Tarjeta: Servicio Seleccionado -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Servicio Seleccionado</span>
                <h3 class="text-base font-extrabold text-sky-800">{{ $referral->service->name }}</h3>
                <div class="flex justify-between text-xs pt-2 border-t border-slate-100">
                    <span class="text-slate-400">Tarifa Regular:</span>
                    <span class="line-through text-slate-400">₡{{ number_format($referral->service->regular_price, 0) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-700 font-bold">Tarifa Referidos:</span>
                    <span class="font-extrabold text-emerald-600">₡30.800</span>
                </div>
            </div>

            <!-- Actualización Rápida de Estado Comercial -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Acción de Estado Rápida</span>
                <form action="{{ route('admin.referrals.update_status', $referral) }}" method="POST" class="space-y-3">
                    @csrf
                    <select name="status" class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 border bg-white">
                        <option value="pending" {{ $referral->status === 'pending' ? 'selected' : '' }}>Nuevo Registrado</option>
                        <option value="in_contact" {{ $referral->status === 'in_contact' ? 'selected' : '' }}>Marcar En Contacto</option>
                        <option value="rejected" {{ $referral->status === 'rejected' ? 'selected' : '' }}>Rechazar Expediente</option>
                        <option value="expired" {{ $referral->status === 'expired' ? 'selected' : '' }}>Marcar Vencido</option>
                    </select>
                    <input type="text" name="note" placeholder="Motivo o detalle del cambio..." class="w-full text-xs rounded-xl border-slate-300 py-1.5 px-3 border">
                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition">
                        Actualizar Estado Comercial
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
