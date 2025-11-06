<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category')?->id;
        return [
            'name'       => ['required','string','max:120'],
            'slug'       => ['nullable','string','max:140','unique:categories,slug,'.($id ?? 'NULL').',id'],
            'icon'       => ['nullable','image','mimes:png,jpg,jpeg','max:2048'],
            'is_active'  => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:65535'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_active'  => $this->boolean('is_active'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
