<?php

namespace App\Http\Resources\Api;

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
            'ip1' => $this->ip1,
            'ip2' => $this->ip2,
            'ip3' => $this->ip3,
            'ip4' => $this->ip4,
            'ip_address' => $this->ip_address,
            'whitelisted' => $this->whitelisted == 1,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'created_by' => $this->created_by,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
