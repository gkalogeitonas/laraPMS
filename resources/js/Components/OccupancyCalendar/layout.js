/**
 * Pure layout logic for the Occupancy Calendar.
 *
 * Dates are plain `YYYY-MM-DD` strings and are handled as UTC calendar days,
 * so daylight-saving changes never shift a column.
 */

const MS_PER_DAY = 24 * 60 * 60 * 1000;

/**
 * @param {string} date `YYYY-MM-DD`
 * @returns {number} days since the Unix epoch
 */
export function toDayNumber(date) {
    const [year, month, day] = date.split('-').map(Number);

    return Math.round(Date.UTC(year, month - 1, day) / MS_PER_DAY);
}

/**
 * @param {number} dayNumber days since the Unix epoch
 * @returns {string} `YYYY-MM-DD`
 */
export function fromDayNumber(dayNumber) {
    return new Date(dayNumber * MS_PER_DAY).toISOString().slice(0, 10);
}

/**
 * The days of the window, one per column.
 *
 * @param {{ start: string, days: number }} window
 * @returns {Array<{ date: string, index: number, utcDate: Date, isWeekend: boolean }>}
 */
export function windowDays(window) {
    const first = toDayNumber(window.start);

    return Array.from({ length: window.days }, (_, index) => {
        const utcDate = new Date((first + index) * MS_PER_DAY);
        const weekday = utcDate.getUTCDay();

        return {
            date: fromDayNumber(first + index),
            index,
            utcDate,
            isWeekend: weekday === 0 || weekday === 6,
        };
    });
}

/**
 * Lays out each Booking as a bar inside the window.
 *
 * Positions are in day columns: a bar starts at the middle of its check-in
 * day (`+ 0.5`) and ends at the middle of its check-out day, so on a Turnover
 * Day the departing and arriving bars meet without overlapping. A bar that
 * starts before or ends after the window is clipped to its edge.
 *
 * Bookings of unknown Rooms and Bookings with no Night inside the window are
 * left out.
 *
 * @param {{ start: string, days: number }} window
 * @param {Array<{ id: number }>} rooms
 * @param {Array<{ id: number, roomId: number, checkIn: string, checkOut: string }>} bookings
 * @returns {Array<{ booking: object, roomId: number, start: number, end: number, clippedStart: boolean, clippedEnd: boolean }>}
 */
export function layoutBars(window, rooms, bookings) {
    const roomIds = new Set(rooms.map((room) => room.id));
    const first = toDayNumber(window.start);
    const windowEnd = window.days;

    return bookings
        .filter((booking) => roomIds.has(booking.roomId))
        .map((booking) => {
            const checkIn = toDayNumber(booking.checkIn) - first;
            const checkOut = toDayNumber(booking.checkOut) - first;

            return { booking, checkIn, checkOut };
        })
        .filter(({ checkIn, checkOut }) => checkOut > checkIn && checkIn < windowEnd && checkOut > 0)
        .map(({ booking, checkIn, checkOut }) => {
            const clippedStart = checkIn < 0;
            const clippedEnd = checkOut >= windowEnd;

            return {
                booking,
                roomId: booking.roomId,
                start: clippedStart ? 0 : checkIn + 0.5,
                end: clippedEnd ? windowEnd : checkOut + 0.5,
                clippedStart,
                clippedEnd,
            };
        });
}
