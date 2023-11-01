<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->display_name ?? $this->name,
            'children' => PermissionChildResource::collection($this->children),
            'created_at' => $this->created_at->format('d-F-Y H:i:s')
        ];
    }
}
