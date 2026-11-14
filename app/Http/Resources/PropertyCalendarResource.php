<?php

namespace App\Http\Resources;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A Property group of the Occupancy Calendar, with its Rooms.
 *
 * @mixin Property
 */
class PropertyCalendarResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, rooms: array<int, array{id: int, name: string}>}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rooms' => $this->rooms
                ->sortBy('name', SORT_NATURAL)
                ->map(fn (Room $room): array => [
                    'id' => $room->id,
                    'name' => $room->name,
                ])
                ->values()
                ->all(),
        ];
    }
}
