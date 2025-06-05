<?php

namespace App\Http\Requests\Routes;

use App\Http\Requests\BaseRequest;

class UpdateRouteRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'total_distance' => 'nullable|numeric',
            'estimated_duration' => 'nullable|integer',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'destinations' => 'sometimes|array',
            'destinations.*' => 'sometimes|exists:destinations,id',
            'tours' => 'sometimes|array',
            'tours.*' => 'sometimes|exists:tours,id',
            'reviews' => 'sometimes|array',
            'reviews.*' => 'sometimes|exists:reviews,id',
        ];
    }
}
