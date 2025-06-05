<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseRequest;

class UpdateRouteStatusRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,in_progress,completed'
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado debe ser uno de: pending, in_progress, completed'
        ];
    }
} 