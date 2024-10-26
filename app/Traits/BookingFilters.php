<?php

namespace App\Traits;

trait BookingFilters
{
    public function applyBookingFilters($query, $request)
    {
        if ($request->has('check_in') && $request->has('check_out')) {
            $query->where(function($query) use ($request) {
                $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                      ->orWhereBetween('check_out', [$request->check_in, $request->check_out]);
            });
        }

        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('booking_status_id')) {
            $query->where('booking_status_id', $request->booking_status_id);
        }

        if ($request->has('booking_source_id')) {
            $query->where('booking_source_id', $request->booking_source_id);
        }

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        return $query;
    }
}
