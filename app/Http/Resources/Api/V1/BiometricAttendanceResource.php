<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BiometricAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'device_id' => $this->device_id,
            'user_id' => $this->user_id,
            'attended_at' => $this->attended_at->toIso8601String(),
            'state' => $this->state,
            'type' => $this->type,
            'ip_address' => $this->ip_address,
            'processed' => $this->processed,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
