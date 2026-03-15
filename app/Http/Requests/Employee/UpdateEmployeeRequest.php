<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $employeeToUpdate = $this->route('employee');
        return $this->user()->tokenCan('*') &&
               $this->user()->clinic_id === $employeeToUpdate->clinic_id;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->route('employee')->id)],
            'role' => ['sometimes', 'required', 'string', Rule::in(['vet', 'recepcionista'])],
        ];
    }
}