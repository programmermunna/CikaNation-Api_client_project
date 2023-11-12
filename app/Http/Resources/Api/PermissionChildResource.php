<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionChildResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->children->count()){
            return [
                $this->name => PermissionChildResource::collection($this->children)
            ];
        }

        return [
            'id'          => $this->id,
            'parent_id'   => $this->parent_id,
            'name'        => $this->display_name ?? $this->name,
            'parent_id'   => $this->parent_id,
            'created_at'  => $this->created_at->format('d-F-Y H:i:s'),
        ];
    }
}
