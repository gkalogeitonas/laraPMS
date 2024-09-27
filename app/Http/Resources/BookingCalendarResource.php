<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingCalendarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'resource' => 'R' . $this->room->id,
            'start' => $this->check_in,
            'end' => $this->check_out,
            'text' => $this->name,
            'color' => '#1155cc', // You can customize the color as needed
        ];
    }
}
