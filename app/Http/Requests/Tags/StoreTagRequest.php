<?php

namespace App\Http\Requests\Tags;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;

class StoreTagRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:tags,name',
            'slug' => 'sometimes|string|max:255|unique:tags,slug',
            'color' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
        ];
    }

    public function prepareForValidation()
    {
        if (!$this->slug) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }
}