<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseRequest;

class UpdateProfilePhotoRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
