<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionTransitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $u = $this->user();
        return $u && (
            (method_exists($u,'hasRole') && ($u->hasRole('Admin') || $u->hasRole('Coordenador')))
            || $u->can('submissions.update')
        );
    }

    public function rules(): array
    {
        return [
            'action'  => ['required','in:desk_reject,request_fixes,send_to_review,accept,reject'],
            'message' => ['nullable','string','max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Selecione uma ação.',
            'action.in'       => 'Ação inválida.',
        ];
    }
}
