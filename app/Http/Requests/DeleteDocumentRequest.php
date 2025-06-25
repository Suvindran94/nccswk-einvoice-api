<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class DeleteDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'string',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
            'staff_id' => [
                'required',
                'integer',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The document ID is required.',
            'id.string' => 'The document ID must be a string.',
            'user_id.required' => 'The user ID is required.',
            'user_id.integer' => 'The user ID must be an integer.',
            'staff_id.required' => 'The staff ID is required.',
            'staff_id.integer' => 'The staff ID must be an integer.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $customMessage = 'An system issue was detected: The request data from the calling module is invalid. Please Contact BIS Team to support.';

        throw new HttpResponseException(response()->json([
            'message' => $customMessage,
            'errors' => $validator->errors(),
        ], 422));
    }
}
