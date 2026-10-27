<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\EvolutionApiService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'referral_benefit_amount' => ['required', 'numeric', 'min:0'],
            'referral_code_prefix' => ['required', 'string', 'max:10'],
            'referral_expiration_days' => ['required', 'integer', 'min:1', 'max:365'],
            'evolution_api_url' => ['nullable', 'url'],
            'evolution_api_key' => ['nullable', 'string', 'max:255'],
            'evolution_instance_name' => ['nullable', 'string', 'max:100'],
            'evolution_admin_group_jid' => ['nullable', 'string', 'max:100'],
            'evolution_auto_notify_referee' => ['nullable', 'boolean'],
            'evolution_auto_notify_referrer' => ['nullable', 'boolean'],
            'evolution_notify_admin_group' => ['nullable', 'boolean'],
        ]);

        foreach ($validated as $key => $value) {
            $type = match (true) {
                in_array($key, ['evolution_auto_notify_referee', 'evolution_auto_notify_referrer', 'evolution_notify_admin_group']) => 'boolean',
                $key === 'referral_benefit_amount' => 'decimal',
                $key === 'referral_expiration_days' => 'integer',
                default => 'string',
            };

            SystemSetting::set($key, $value ?? '', $type);
        }

        return back()->with('status', 'Configuración del sistema actualizada correctamente.');
    }

    public function testEvolution(Request $request, EvolutionApiService $evolutionService)
    {
        $groupJid = SystemSetting::get('evolution_admin_group_jid');
        if (empty($groupJid)) {
            return back()->withErrors(['evolution_admin_group_jid' => 'Debe ingresar un JID de grupo administrativo válido para realizar la prueba.']);
        }

        $testMessage = "🧪 *PRUEBA DE CONEXIÓN EVOLUTION API - AQUATICA REFERIDOS*\n\n"
            . "La integración con el sistema autónomo de referidos está respondiendo satisfactoriamente.\n"
            . "Fecha y hora: " . now()->format('Y-m-d H:i:s');

        $result = $evolutionService->sendTextMessage($groupJid, $testMessage, null, 'admin_test');

        if ($result['success']) {
            return back()->with('status', '¡Mensaje de prueba enviado con éxito al grupo administrativo!');
        }

        return back()->withErrors(['test' => 'Error al comunicar con Evolution API: ' . ($result['error'] ?? 'Verifique la URL y API Key.')]);
    }
}
