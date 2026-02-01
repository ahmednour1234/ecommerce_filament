<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBiometricLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'serial_number' => 'required|string',
            'ip_address' => 'nullable|ip',
            'logs' => 'required|array|max:1000',
            'logs.*.user_id' => 'required|string',
            'logs.*.timestamp' => 'required|date',
            'logs.*.state' => 'nullable|integer',
            'logs.*.type' => 'nullable|integer',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400)
        );
    }
}
