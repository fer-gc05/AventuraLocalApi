<?php

namespace App\Http\Requests\Events;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;

class updateEventRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'sometimes|date|after:now',
            'end_datetime' => 'sometimes|date|after:start_datetime',
            'location' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:COP',
            'max_attendees' => 'nullable|integer|min:1',
            'destination_id' => 'sometimes|exists:destinations,id',
        ];

    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->title),
        ]);
    }
}
