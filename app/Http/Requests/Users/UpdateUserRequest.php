<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseRequest;

class UpdateUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|string|exists:roles,name|in:Traveler,Entrepreneur,Event Organizer,Event Participant'
        ];
    }
}
