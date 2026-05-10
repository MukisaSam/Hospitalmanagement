<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'       => 'required|integer|exists:patients,id',
            'doctor_id'        => 'required|integer|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'type'             => 'required|in:opd,ipd,emergency,follow_up',
            'notes'            => 'nullable|string',
            'reason'           => 'required|string|min:10',
        ];
    }
}
