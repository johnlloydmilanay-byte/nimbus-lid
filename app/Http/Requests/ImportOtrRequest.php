<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportOtrRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Or add your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'skip_duplicates' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'excel_file.required' => 'Please select an Excel file to import.',
            'excel_file.mimes' => 'The file must be an Excel file (xlsx, xls, csv).',
            'excel_file.max' => 'The file size must not exceed 10MB.',
        ];
    }
}