<?php

namespace App\Http\Requests\Routes;

use App\Http\Requests\BaseRequest;

class AttachCommunityRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'community_id' => 'required|exists:communities,id'
        ];
    }

    public function messages(): array
    {
        return [
            'community_id.required' => 'El ID de la comunidad es requerido',
            'community_id.exists' => 'La comunidad seleccionada no existe'
        ];
    }
} 