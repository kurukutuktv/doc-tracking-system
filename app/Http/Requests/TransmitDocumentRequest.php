<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransmitDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transmission_mode_id' => ['required', 'exists:transmission_modes,id'],
        ];
    }
}

