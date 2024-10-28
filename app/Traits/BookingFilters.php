<?php

namespace App\Traits;

trait BookingFilters
{
    public function applyBookingFilters($query, $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->where(function($query) use ($request) {
                $query->where('check_in', '<=', $request->end_date)
                ->where('check_out', '>=', $request->start_date);
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

