<?php

namespace App\Http\Requests\Routes;

use App\Http\Requests\BaseRequest;

class AttachEventRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|exists:events,id'
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required' => 'El ID del evento es requerido',
            'event_id.exists' => 'El evento seleccionado no existe'
        ];
    }
} 