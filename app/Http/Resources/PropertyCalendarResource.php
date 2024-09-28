<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyCalendarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'id' => 'P' . $this->id,
            'expanded' => true,
            'children' => $this->rooms->map(function ($room) {
                return [
                    'name' => $room->name,
                    'id' => 'R' . $room->id,
                ];
            }),
        ];
    }
}
