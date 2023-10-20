<?php

namespace Polygon\OTP\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorizationCode extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'authorization_code' => $this->getCode(),
        ];
    }
}
