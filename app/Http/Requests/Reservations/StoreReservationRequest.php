<?php

namespace App\Http\Requests\Reservations;

use App\Http\Requests\BaseRequest;

class StoreReservationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'tour_id' => 'nullable|exists:tours,id',
            'destination_id' => 'nullable|exists:destinations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'participants' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
        ];
    }
}
