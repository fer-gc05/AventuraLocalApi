<?php

namespace App\Http\Requests\Events;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;

class StoreEventRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:COP', 
            'max_attendees' => 'nullable|integer|min:1',
            'destination_id' => 'required|exists:destinations,id',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->title),
        ]);
    }
}
