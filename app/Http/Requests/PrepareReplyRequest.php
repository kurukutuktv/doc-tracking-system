<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrepareReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type_id' => ['required', 'exists:document_types,id'],
            'priority_level'   => ['nullable', 'in:LOW,NORMAL,HIGH,URGENT'],
            'title'            => ['required', 'string', 'max:255'],
            'subject'          => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'file_path'        => ['required', 'string'],

            // FILE VALIDATION
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,jpg,png',
                'max:10240', // 10MB
            ],
        ];
    }
}
