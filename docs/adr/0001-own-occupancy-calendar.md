# Build our own Occupancy Calendar instead of using DayPilot

---
status: accepted
---

The calendar page used `daypilot-pro-vue`, a commercial scheduler that we only had as a 2023 trial tarball downloaded from the vendor's URL. We need only a small part of what it offers: Rooms grouped by Property against days, Booking bars, Reschedule by drag and resize, and Overbookings shown visibly. So we are replacing it with our own Vue component, `OccupancyCalendar`, that adds no new dependencies.

The component knows nothing about tenants, Eloquent models or ids. It takes a fixed, plain prop shape (Property groups with Rooms, plus Bookings), and it emits a `reschedule` event. All mapping from models happens in the calendar API Resources and the `Calendar` page. We keep this boundary because the tenancy model may be replaced later, for example by Laravel-style teams. When that happens, only the mapping layer should change.

## Considered Options

- **Keep DayPilot Pro**: rejected because it needs a paid licence, and the trial build can't be used in production.
- **Another scheduler library**: rejected because it adds a heavy dependency for a feature set small enough to own.
