<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class DestroyEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $employeeToDelete = $this->route('employee');
        
        $admin = $this->user();
        $isAdmin = $admin->tokenCan('*');

        //admin só pode deletar funcionários da sua própria clínica
        $isSameClinic = $admin->clinic_id === $employeeToDelete->clinic_id;

        //admin NÃO pode inativar a si próprio
        $isNotSelfDelete = $admin->id !== $employeeToDelete->id;

        return $isAdmin && $isSameClinic && $isNotSelfDelete;
    }

    public function rules(): array
    {
        return [];
    }

    // protected function failedAuthorization()
    // {
    //     abort(403, 'Ação não autorizada ou você não pode inativar a si mesmo.');
    // }
}