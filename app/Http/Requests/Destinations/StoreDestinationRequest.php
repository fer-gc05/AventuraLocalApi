<?php

namespace App\Http\Requests\Destinations;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;

class StoreDestinationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:destinations,name',
            'short_description' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'price' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'opening_hours' => 'required|string',
            'contact_phone' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'required|array',
            'tags.*' => 'required|exists:tags,id',
            'media' => 'nullable|array',
            'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name)
        ]);
    }
}