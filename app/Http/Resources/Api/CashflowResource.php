<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashflowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'item_name'  => $this->item_name,
            'item_price' => $this->item_price,
            'uploads'    => $this->upload,
            'created_at' => $this->created_at->format('d-F-Y H:i A')
        ];
    }
}
