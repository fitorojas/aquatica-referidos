<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Referente
            'referrer.full_name' => ['required', 'string', 'min:3', 'max:150'],
            'referrer.phone' => ['required', 'string', 'min:8', 'max:25'],
            'referrer.identification' => ['nullable', 'string', 'max:30'],

            // Referido (Alumno)
            'referee.full_name' => ['required', 'string', 'min:3', 'max:150'],
            'referee.phone' => ['required', 'string', 'min:8', 'max:25'],
            'referee.identification' => ['nullable', 'string', 'max:30'],

            // Servicio & Términos
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'notes' => ['nullable', 'string', 'max:600'],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'referrer.full_name.required' => 'Por favor indique el nombre completo de la persona que refiere.',
            'referrer.phone.required' => 'El teléfono de quien refiere es obligatorio.',
            'referee.full_name.required' => 'Por favor indique el nombre de la persona referida / alumno.',
            'referee.phone.required' => 'El teléfono de la persona referida es obligatorio para contactarle.',
            'service_id.required' => 'Seleccione uno de los 5 servicios participantes.',
            'service_id.exists' => 'El servicio seleccionado no es válido.',
            'terms.accepted' => 'Debe aceptar los términos y condiciones del programa de referidos.',
        ];
    }
}
