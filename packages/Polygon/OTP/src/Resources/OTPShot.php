<?php

namespace Polygon\OTP\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OTPShot extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'retry_after' => $this->retry_after->diffInSeconds(),
        ];
    }
}
