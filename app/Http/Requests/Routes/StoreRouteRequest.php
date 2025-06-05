<?php

namespace App\Http\Requests\Routes;

use App\Http\Requests\BaseRequest;

class StoreRouteRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_distance' => 'nullable|numeric',
            'estimated_duration' => 'nullable|integer',
            'difficulty' => 'required|in:easy,medium,hard',
            'destinations' => 'required|array',
            'destinations.*' => 'required|exists:destinations,id',
            'tours' => 'nullable|array',
            'tours.*' => 'nullable|exists:tours,id',
        ];
    }
}
