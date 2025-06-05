<?php

namespace App\Http\Requests\Reviews;

use App\Http\Requests\BaseRequest;

class StoreReviewRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'reviewable_type' => 'required|string|in:route,destination,event',
            'reviewable_id' => 'required|integer',
        ];
    }
}
