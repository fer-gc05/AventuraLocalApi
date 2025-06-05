<?php

namespace App\Http\Requests\Reviews;

use App\Http\Requests\BaseRequest;

class UpdateReviewRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
        ];
    }
}
