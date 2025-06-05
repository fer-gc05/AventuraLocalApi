<?php

namespace App\Http\Requests\Communities;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;

class UpdateCommunityRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:communities,slug,' . $this->community->id,
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'is_public' => 'sometimes|boolean',
            'media' => 'sometimes|array',
            'media.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }
}