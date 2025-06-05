<?php

namespace App\Http\Requests\Reservations;

use App\Http\Requests\BaseRequest;

class UpdateReservationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'tour_id' => 'sometimes|exists:tours,id',
            'destination_id' => 'sometimes|exists:destinations,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'participants' => 'sometimes|integer|min:1',
            'total_price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:3',
        ];
    }
}
