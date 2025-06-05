<?php

namespace App\Http\Requests\Tours;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;

class UpdateTourRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:3',
            'duration_days' => 'sometimes|integer|min:1',
            'status' => 'sometimes|string|in:active,inactive',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
    }
}
