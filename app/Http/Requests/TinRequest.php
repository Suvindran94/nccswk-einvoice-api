<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class TinRequest extends FormRequest
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
            'tin' => [
                'required',
                'string'
            ],
            'id_type' => [
                'required',
                'string',
                'in:NRIC,PASSPORT,BRN,ARMY'
            ],
            'id_value' => [
                'required',
                'string'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'tin.required' => 'The Tin Number is required.',
            'tin.string' => 'The Tin Number must be a string.',

            'id_type.required' => 'The Business Registration Number is required.',
            'id_type.string' => 'The Business Registration Number must be a string.',
            'id_type.in' => 'The Business Registration Number is invalid. It must be one of the following: NRIC, PASSPORT, BRN, or ARMY.',

            'id_value.required' => 'The Identification Number is required.',
            'id_value.integer' => 'The Identification Number must be an integer.',
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
