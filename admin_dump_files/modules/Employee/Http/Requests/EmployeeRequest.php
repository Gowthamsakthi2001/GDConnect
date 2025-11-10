<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:employees,name',
            'payroll_type' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'nid' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'email2' => 'nullable|email|max:255',
            'phone2' => 'nullable|string|max:255',
            'join_date' => 'required|date',
            'blood_group' => 'nullable|string|max:255',
            'dob' => 'required|date',
            'working_slot_from' => 'nullable|string|max:255',
            'working_slot_to' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'present_contact' => 'nullable|string|max:255',
            'present_address' => 'nullable|string|max:255',
            'permanent_contact' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:255',
            'present_city' => 'nullable|string|max:255',
            'permanent_city' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_mobile' => 'nullable|string|max:255',
            'reference_name' => 'nullable|string|max:255',
            'reference_mobile' => 'nullable|string|max:255',
            'reference_email' => 'nullable|email|max:255',
            'reference_address' => 'nullable|string|max:255',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'required|string|max:255';
        }

        return $rules;
    }
}
