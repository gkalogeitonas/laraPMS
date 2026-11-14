# laraPMS

A multi-tenant property-management system: tenants manage their properties, rooms and the bookings that occupy them.

## Language

### Inventory

**Property**:
A building or complex a tenant operates, grouping one or more Rooms.
_Avoid_: Hotel, building, resource

**Room**:
A single bookable unit inside a Property; the thing a Booking occupies.
_Avoid_: Unit, resource

### Bookings

**Booking**:
A reservation of one Room for a span of Nights, from check-in date to check-out date.
_Avoid_: Event, reservation, stay

**Night**:
The unit a Booking occupies; a Booking covers every Night from its check-in date up to, but not including, its check-out date.
_Avoid_: Day

**Turnover Day**:
A date on which one Booking checks out of a Room and another checks in; the two Bookings do not overlap.
_Avoid_: Changeover

**Overbooking**:
Two or more Bookings of the same Room that share at least one Night. It should be prevented, but can still happen and must stay visible when it does.
_Avoid_: Conflict, double booking, overlap

### Views

**Occupancy Calendar**:
The view that lays out Rooms (grouped by Property) against dates and shows each Booking as a bar across its Nights.
_Avoid_: Scheduler, timeline
