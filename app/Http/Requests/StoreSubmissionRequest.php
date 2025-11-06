<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
  public function authorize(): bool { return $this->user()?->can('submissions.create') ?? false; }
  public function rules(): array {
    return [
      'title'    => ['required','string','max:180'],
      'abstract' => ['nullable','string','max:10000'],
      'language' => ['nullable','string','max:10'],
      'keywords' => ['nullable','array','max:12'],
      'keywords.*' => ['string','max:40'],
    ];
  }
}
