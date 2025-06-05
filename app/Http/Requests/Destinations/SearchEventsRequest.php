<?php

namespace App\Http\Requests\Destinations;

use App\Http\Requests\BaseRequest;

class SearchEventsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'destination_id' => 'required|exists:destinations,id',
        ];
    }
}
