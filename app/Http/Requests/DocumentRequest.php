<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DocumentRequest extends FormRequest
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
            'approve_status' => [
                'required',
                'string',
                'in:A,R',
            ],
            'notification_id' => [
                'required',
                'integer',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
            'is_einvoice_required' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The document ID is required.',
            'id.string' => 'The document ID must be a string.',

            'approve_status.required' => 'The approval status is required.',
            'approve_status.string' => 'The approval status must be a string.',
            'approve_status.in' => 'The approval status must be either "A" (Approved) or "R" (Rejected).',

            'notification_id.required' => 'The notification ID is required.',
            'notification_id.integer' => 'The notification ID must be an integer.',

            'user_id.required' => 'The user ID is required.',
            'user_id.integer' => 'The user ID must be an integer.',

            'is_einvoice_required.required' => 'The "is E-Invoice required" flag is mandatory.',
            'is_einvoice_required.boolean' => 'The "is E-Invoice required" flag must be a boolean (true/false or 1/0).',
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
