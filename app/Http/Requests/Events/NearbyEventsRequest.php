<?php

namespace App\Http\Requests\Events;

use App\Http\Requests\BaseRequest;

class NearbyEventsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
        'event_id' => 'required|exists:events,id',
        'radius' => 'nullable|numeric|min:1|max:100',
        'searchTerm' => 'nullable|string|max:255',
        ];
    }
}
