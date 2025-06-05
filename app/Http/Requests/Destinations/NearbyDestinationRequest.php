<?php

namespace App\Http\Requests\Destinations;

use App\Http\Requests\BaseRequest;

class NearbyDestinationRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'destination_id' => 'required|exists:destinations,id',
            'radius' => 'sometimes|numeric|min:0.1|max:1000',
            'searchTerm' => 'sometimes|string|max:255',
            'limit' => 'sometimes|integer|min:1|max:50'
        ];
    }
}
