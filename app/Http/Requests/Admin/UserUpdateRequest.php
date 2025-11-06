<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.manage') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('user')?->id;

        return [
            'name'     => ['required','string','max:120'],

            'email'    => ['required_without:cpf','nullable','email','max:160', Rule::unique('users','email')->ignore($id)],
            'cpf'      => ['required_without:email','nullable','digits:11', Rule::unique('users','cpf')->ignore($id)],

            'password' => ['nullable','string','min:8','confirmed'],
            'role'     => ['required', Rule::in(['Admin','Coordenador','Revisor','Autor'])],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required_without' => 'Informe e-mail ou CPF.',
            'cpf.required_without'   => 'Informe CPF ou e-mail.',
            'cpf.digits'             => 'CPF deve ter 11 dígitos (somente números).',
            'role.in'                => 'Selecione um papel válido.',
        ];
    }
}
