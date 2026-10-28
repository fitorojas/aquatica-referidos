@extends('layouts.public')

@section('title', 'Condiciones Oficiales de la Campaña de Referidos | AQUATICA')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <!-- Encabezado con Botón de Descarga PDF -->
    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm mb-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-sky-100 text-sky-800 mb-2">
                Documento Oficial · Vigencia desde el 28 de octubre de 2026
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Condiciones del Programa de Referidos
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                AQUATICA · Natación para todos
            </p>
        </div>

        <a href="{{ asset('docs/condiciones-programa-referidos-aquatica.pdf') }}" target="_blank"
            class="inline-flex items-center px-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-md shadow-rose-600/20 transition transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Descargar Documento PDF (2 Páginas)
        </a>
    </div>

    <!-- Contenido de las 11 Cláusulas Oficiales -->
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm space-y-8">
        <!-- 1. Objetivo -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">1. Objetivo</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                Agradecer la confianza de nuestros clientes actuales y facilitar que más personas conozcan los programas de natación de AQUATICA mediante recomendaciones personales.
            </p>
        </div>

        <!-- 2. ¿Quién puede participar? -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">2. ¿Quién puede participar?</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                Pueden recomendar los clientes de AQUATICA con matrícula activa, mensualidad regular de ₡35.000 y pagos al día. La persona referida debe ser un alumno nuevo. La campaña no modifica tarifas históricas inferiores ni se combina con otras promociones o descuentos.
            </p>
        </div>

        <!-- 3. Beneficio económico -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">3. Beneficio económico</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                Cuando se concrete una recomendación válida, tanto el cliente que recomienda como el nuevo alumno referido recibirán una <strong>mensualidad especial de ₡30.800</strong>. Para quien paga ₡35.000, representa un ahorro mensual de ₡4.200. El beneficio no es acumulable por múltiples referidos.
            </p>
        </div>

        <!-- 4. ¿Qué cuenta como recomendación válida? -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">4. ¿Qué cuenta como recomendación válida?</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                La persona referida debe ser un alumno nuevo de AQUATICA, completar su inscripción y pagar su primera mensualidad. No cuentan traslados de alumnos ya matriculados. El cliente que recomienda debe registrar al referido antes de que este formalice la matrícula.
            </p>
        </div>

        <!-- 5. ¿Cuándo se aplica la tarifa especial? -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">5. ¿Cuándo se aplica la tarifa especial?</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                El nuevo alumno podrá pagar ₡30.800 desde su primera mensualidad, una vez confirmada su elegibilidad. Para el cliente que recomienda, la tarifa se aplicará a partir de su siguiente mensualidad aún no pagada, después de verificar la matrícula y el primer pago del referido. No habrá ajustes retroactivos ni devoluciones.
            </p>
        </div>

        <!-- 6. Duración: beneficio permanente -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">6. Duración: beneficio permanente</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                Ambas personas conservan de forma independiente la tarifa de ₡30.800 mientras mantengan su matrícula activa y sus pagos al día. No se exige permanencia mínima del referido después de su matrícula válida. Si una persona se retira, la otra conserva el beneficio mientras cumpla sus condiciones.
            </p>
        </div>

        <!-- 7. Retiro y reingreso -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">7. Retiro y reingreso</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                El retiro de una persona no cancela el beneficio de la otra. Quien deje de estar matriculado pierde su tarifa promocional. Si vuelve, pagará la tarifa normal vigente para nuevos ingresos; el beneficio anterior no se recupera automáticamente.
            </p>
        </div>

        <!-- 8. Pagos y continuidad -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">8. Pagos y continuidad</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                Cada beneficiario debe pagar puntualmente y mantener su matrícula activa. Las ausencias a clases no implican retiro si está al día y cumple las políticas de AQUATICA. La suspensión de matrícula o la pérdida de condición de cliente activo termina el beneficio de esa persona.
            </p>
        </div>

        <!-- 9. Registro, validación y casos especiales -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">9. Registro, validación y casos especiales</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                La administración registrará los nombres de ambas personas y las fechas de registro y matrícula. Si varios clientes recomiendan al mismo alumno, se reconocerá a quien lo registró primero, antes de la matrícula. Cada matrícula nueva puede generar un solo par de beneficios. Los casos especiales se resolverán individualmente.
            </p>
        </div>

        <!-- 10. Alcance y cambios futuros -->
        <div class="border-b border-slate-100 pb-6">
            <h2 class="text-base font-bold text-sky-600 mb-2">10. Alcance y cambios futuros</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                La campaña aplica a nuevas recomendaciones registradas desde su lanzamiento. AQUATICA podrá cerrar la recepción de nuevos referidos o modificar las condiciones para participaciones futuras, comunicándolo previamente. Los beneficios ya obtenidos se respetarán mientras sus titulares cumplan las condiciones de continuidad aquí establecidas.
            </p>
        </div>

        <!-- 11. Consultas y confirmación -->
        <div>
            <h2 class="text-base font-bold text-sky-600 mb-2">11. Consultas y confirmación</h2>
            <p class="text-sm text-slate-700 leading-relaxed">
                Para registrar un referido, confirmar la aplicación del descuento o consultar cualquier condición, comunicarse con la administración de AQUATICA antes de la matrícula del nuevo alumno.
            </p>
        </div>
    </div>

    <!-- Botón Inferior -->
    <div class="mt-8 text-center">
        <a href="{{ route('public.referral.create') }}" class="inline-flex items-center px-6 py-3.5 rounded-2xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm shadow-md transition">
            ← Volver al Formulario de Registro de Referidos
        </a>
    </div>
</div>
@endsection
