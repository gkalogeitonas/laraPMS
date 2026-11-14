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
 * @param {string} date `YYYY-MM-DD`
 * @returns {Date} midnight UTC of that date, for `Intl` formatting with `timeZone: 'UTC'`
 */
export function toUtcDate(date) {
    return new Date(toDayNumber(date) * MS_PER_DAY);
}

/**
 * @param {number} dayNumber days since the Unix epoch
 * @returns {string} `YYYY-MM-DD`
 */
function fromDayNumber(dayNumber) {
    return new Date(dayNumber * MS_PER_DAY).toISOString().slice(0, 10);
}

/**
 * @param {string} date `YYYY-MM-DD`
 * @param {number} days may be negative
 * @returns {string} the date `days` later, `YYYY-MM-DD`
 */
export function addDays(date, days) {
    return fromDayNumber(toDayNumber(date) + days);
}

/**
 * @returns {string} today's date in the viewer's time zone, `YYYY-MM-DD`
 */
export function localToday() {
    const now = new Date();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    return `${now.getFullYear()}-${month}-${day}`;
}

/**
 * The days of the window, one per column.
 *
 * @param {{ start: string, days: number }} dateWindow
 * @returns {Array<{ date: string, index: number, utcDate: Date, isWeekend: boolean }>}
 */
export function windowDays(dateWindow) {
    const first = toDayNumber(dateWindow.start);

    return Array.from({ length: dateWindow.days }, (_, index) => {
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
 * Lays out the Occupancy Calendar: each Booking as a bar, the stacking lanes
 * of each Room, and the Overbookings in the window.
 *
 * Positions are in day columns: a bar starts at the middle of its check-in
 * day (`+ 0.5`) and ends at the middle of its check-out day, so on a Turnover
 * Day the departing and arriving bars meet without overlapping. A bar that
 * starts before or ends after the window is clipped to its edge.
 *
 * Bookings of the same Room that share a Night get different lanes, and a Room
 * has as many lanes as it needs (at least one). A Turnover Day shares no Night,
 * so it never adds a lane.
 *
 * An Overbooking is reported for each run of consecutive Nights of a Room that
 * two or more Bookings share, so it counts what the user sees as one red
 * patch: a three-way overlap is one Overbooking, and one long Booking that
 * overlaps two separate short ones makes two. Its span covers those Nights (Night `n` runs from
 * the middle of day `n` to the middle of day `n + 1`), clipped to the window.
 * Overbookings follow the order of `rooms`, then their dates.
 *
 * Every Booking counts as an Active Booking. Bookings of unknown Rooms and
 * Bookings with no Night inside the window are left out.
 *
 * @param {{ start: string, days: number }} dateWindow
 * @param {Array<{ id: number }>} rooms
 * @param {Array<{ id: number, roomId: number, checkIn: string, checkOut: string }>} bookings
 * @returns {{
 *     bars: Array<{ booking: object, roomId: number, lane: number, start: number, end: number, clippedStart: boolean, clippedEnd: boolean }>,
 *     laneCounts: Map<number, number>,
 *     overbookings: Array<{ roomId: number, start: number, end: number }>,
 * }}
 */
export function layoutCalendar(dateWindow, rooms, bookings) {
    const first = toDayNumber(dateWindow.start);
    const dayCount = dateWindow.days;
    const spansByRoom = new Map(rooms.map((room) => [room.id, []]));

    for (const booking of bookings) {
        const checkIn = toDayNumber(booking.checkIn) - first;
        const checkOut = toDayNumber(booking.checkOut) - first;
        const hasNightInWindow = checkOut > checkIn && checkIn < dayCount && checkOut > 0;

        if (spansByRoom.has(booking.roomId) && hasNightInWindow) {
            spansByRoom.get(booking.roomId).push({ booking, checkIn, checkOut });
        }
    }

    const bars = [];
    const laneCounts = new Map();
    const overbookings = [];

    for (const [roomId, spans] of spansByRoom) {
        const { lanes, laneCount } = assignLanes(spans);
        laneCounts.set(roomId, Math.max(laneCount, 1));

        for (const { booking, checkIn, checkOut } of spans) {
            const clippedStart = checkIn < 0;
            const clippedEnd = checkOut >= dayCount;

            bars.push({
                booking,
                roomId,
                lane: lanes.get(booking),
                start: clippedStart ? 0 : checkIn + 0.5,
                end: clippedEnd ? dayCount : checkOut + 0.5,
                clippedStart,
                clippedEnd,
            });
        }

        for (const run of sharedNightRuns(spans, dayCount)) {
            overbookings.push({
                roomId,
                start: Math.max(run.firstNight + 0.5, 0),
                end: Math.min(run.lastNight + 1.5, dayCount),
            });
        }
    }

    return { bars, laneCounts, overbookings };
}

/**
 * Gives each Booking the lowest lane that is free from its check-in, taking
 * Bookings by check-in date. This uses the fewest lanes possible.
 *
 * @param {Array<{ booking: object, checkIn: number, checkOut: number }>} spans Bookings of one Room, as day offsets from the window start
 * @returns {{ lanes: Map<object, number>, laneCount: number }} lane per Booking, and the number of lanes used
 */
function assignLanes(spans) {
    const ordered = [...spans].sort((a, b) => a.checkIn - b.checkIn || b.checkOut - a.checkOut || a.booking.id - b.booking.id);
    const laneEnds = [];
    const lanes = new Map();

    for (const span of ordered) {
        let lane = laneEnds.findIndex((end) => end <= span.checkIn);
        if (lane === -1) {
            lane = laneEnds.length;
        }
        laneEnds[lane] = span.checkOut;
        lanes.set(span.booking, lane);
    }

    return { lanes, laneCount: laneEnds.length };
}

/**
 * Runs of consecutive Nights shared by two or more Bookings. The Night before
 * the window (`-1`) is checked too, because its second half is visible: an
 * Overbooking that started earlier is then highlighted from the window's edge.
 *
 * @param {Array<{ checkIn: number, checkOut: number }>} spans Bookings of one Room, as day offsets from the window start
 * @param {number} dayCount
 * @returns {Array<{ firstNight: number, lastNight: number }>}
 */
function sharedNightRuns(spans, dayCount) {
    const earliestNight = -1;
    const bookingsPerNight = new Array(dayCount - earliestNight).fill(0);

    for (const { checkIn, checkOut } of spans) {
        for (let night = Math.max(checkIn, earliestNight); night < Math.min(checkOut, dayCount); night++) {
            bookingsPerNight[night - earliestNight] += 1;
        }
    }

    const runs = [];
    let firstNight = null;

    bookingsPerNight.forEach((count, index) => {
        const night = index + earliestNight;
        if (count >= 2 && firstNight === null) {
            firstNight = night;
        } else if (count < 2 && firstNight !== null) {
            runs.push({ firstNight, lastNight: night - 1 });
            firstNight = null;
        }
    });

    if (firstNight !== null) {
        runs.push({ firstNight, lastNight: dayCount - 1 });
    }

    return runs;
}
