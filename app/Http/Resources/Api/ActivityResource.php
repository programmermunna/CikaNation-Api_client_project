<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'log_name'      => $this->log_name,
            'description'   => $this->description,
            'ip'            => json_decode($this->properties)->ip,
            'target'        => json_decode($this->properties)->target,
            'activity'      => json_decode($this->properties)->activity,
            'cause_by'      => [
                'id' => $this->causer->id,
                'name' => $this->causer->name,
                'username' => $this->causer->username,
                'email'    => $this->causer->email,
                'created_at' => $this->causer->created_at,
            ],
            'created_at'    => $this->created_at->format('d-F-Y h:i'),
            'human_diff'    => $this->created_at->diffForHumans(),
            
        ];
    }
}
