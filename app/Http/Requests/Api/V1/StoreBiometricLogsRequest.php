<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

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
}
