<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserIpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'ip1' => $this->ip1,
            'ip2' => $this->ip2,
            'ip3' => $this->ip3,
            'ip4' => $this->ip4,
            'ip_address' => $this->ip_address,
            'whitelisted' => $this->whitelisted == 1 ? true : false,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
        ];
    }
}
