<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // date_of_birth is immutable — not included in update rules
        return [
            'first_name'              => 'required|string|max:100',
            'last_name'               => 'required|string|max:100',
            'gender'                  => 'required|in:male,female,other',
            'phone_number'            => 'required|string|max:20',
            'address'                 => 'nullable|string|max:500',
            'email'                   => 'nullable|email|max:255|unique:patients,email,' . $this->patient?->id,
            'national_id'             => 'nullable|string|max:50',
            'blood_group'             => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'marital_status'          => 'nullable|in:single,married,divorced,widowed',
            'allergies'               => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ];
    }
}
