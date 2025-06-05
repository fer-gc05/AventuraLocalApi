<?php

namespace App\Http\Requests\Destinations;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;

class UpdateDestinationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'short_description' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'zip_code' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'price' => 'sometimes|numeric',
            'currency' => 'sometimes|string|max:3',
            'opening_hours' => 'sometimes|string',
            'contact_phone' => 'sometimes|string|max:255',
            'contact_email' => 'sometimes|email|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'sometimes|string|exists:tags,id',
            'media' => 'sometimes|array',
            'media.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
    }
}
