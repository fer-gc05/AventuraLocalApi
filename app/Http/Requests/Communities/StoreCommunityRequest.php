<?php

namespace App\Http\Requests\Communities;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;

class StoreCommunityRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:communities,slug',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'is_public' => 'required|boolean',
            'media' => 'nullable|array',
            'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
    }
}