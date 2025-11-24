<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offering_id' => ['required', 'exists:offerings,id'],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
                'before:' . now()->addYear()->toDateString(),
            ],
            'end_date' => [
                'nullable',
                'date',
                'after:start_date',
                'before:' . now()->addYear()->toDateString(),
            ],
            'guests' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = Carbon::parse($this->start_date);
            $end = $this->end_date ? Carbon::parse($this->end_date) : $start;
            $days = $start->diffInDays($end);
            
            if ($days > 365) {
                $validator->errors()->add('end_date', 'Booking duration cannot exceed 365 days');
            }
        });
    }
}
