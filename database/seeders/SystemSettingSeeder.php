<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'referral_benefit_amount',
                'value' => '30800.00',
                'type' => 'decimal',
                'description' => 'Monto oficial de la mensualidad promocional para referente y referido (en colones).',
            ],
            [
                'key' => 'referral_code_prefix',
                'value' => 'AQ-REF-',
                'type' => 'string',
                'description' => 'Prefijo alfanumérico para el código de expediente de referidos.',
            ],
            [
                'key' => 'referral_expiration_days',
                'value' => '60',
                'type' => 'integer',
                'description' => 'Días de vigencia del expediente desde su registro inicial.',
            ],
            [
                'key' => 'evolution_api_url',
                'value' => config('evolution.api_url', 'https://evolution.example.com'),
                'type' => 'string',
                'description' => 'URL base de la instancia de Evolution API.',
            ],
            [
                'key' => 'evolution_api_key',
                'value' => config('evolution.api_key', ''),
                'type' => 'string',
                'description' => 'API Key de autenticación en Evolution API.',
            ],
            [
                'key' => 'evolution_instance_name',
                'value' => 'aquatica_referidos',
                'type' => 'string',
                'description' => 'Nombre de la instancia de WhatsApp en Evolution API.',
            ],
            [
                'key' => 'evolution_admin_group_jid',
                'value' => '',
                'type' => 'string',
                'description' => 'JID del grupo de WhatsApp administrativo para alertas automáticas.',
            ],
            [
                'key' => 'evolution_admin_phones',
                'value' => '',
                'type' => 'string',
                'description' => 'Números individuales de WhatsApp a notificar (separados por coma o saltos de línea).',
            ],
            [
                'key' => 'evolution_auto_notify_referee',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Habilitar mensaje automático de bienvenida por WhatsApp al referido.',
            ],
            [
                'key' => 'evolution_auto_notify_referrer',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Habilitar mensaje automático por WhatsApp al referente al avanzar etapas.',
            ],
            [
                'key' => 'evolution_notify_admin_group',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Habilitar alertas inmediatas al grupo administrativo de WhatsApp.',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
