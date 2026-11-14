<script setup>
import { computed, nextTick, reactive, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ExclamationTriangleIcon } from '@heroicons/vue/20/solid';
import { layoutCalendar, localToday, toDayNumber, toUtcDate, windowDays } from './layout.js';

const props = defineProps({
    /** @type {Array<{ id: number, name: string, rooms: Array<{ id: number, name: string }> }>} */
    groups: { type: Array, required: true },
    /** @type {Array<{ id: number, roomId: number, checkIn: string, checkOut: string, guestName: string, guests: number, nights: number, totalCost: number, sourceName: ?string, statusName: ?string, color: ?string, editUrl: string }>} */
    bookings: { type: Array, required: true },
    /** @type {{ start: string, days: number }} */
    window: { type: Object, required: true },
});

const DAY_WIDTH = 44;
const ROW_PADDING = 6;
const BAR_HEIGHT = 32;
const LANE_GAP = 4;
const BAR_INSET = 2;
const NEUTRAL_COLOR = '#9ca3af';

const monthFormatter = new Intl.DateTimeFormat('en', { month: 'long', year: 'numeric', timeZone: 'UTC' });
const weekdayFormatter = new Intl.DateTimeFormat('en', { weekday: 'short', timeZone: 'UTC' });
const longDateFormatter = new Intl.DateTimeFormat('en', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC' });
const costFormatter = new Intl.NumberFormat('en', { style: 'currency', currency: 'EUR' });

const days = computed(() => windowDays(props.window));
const timelineWidth = computed(() => props.window.days * DAY_WIDTH);

const months = computed(() => {
    const spans = [];
    for (const day of days.value) {
        const label = monthFormatter.format(day.utcDate);
        const last = spans[spans.length - 1];
        if (last && last.label === label) {
            last.days += 1;
        } else {
            spans.push({ label, days: 1 });
        }
    }

    return spans;
});

const todayIndex = computed(() => toDayNumber(localToday()) - toDayNumber(props.window.start));
const showTodayLine = computed(() => todayIndex.value >= 0 && todayIndex.value < props.window.days);

const layout = computed(() => layoutCalendar(props.window, props.groups.flatMap((group) => group.rooms), props.bookings));

/** Groups items that have a `roomId` by Room. */
function byRoom(items) {
    const grouped = new Map();
    for (const item of items) {
        if (!grouped.has(item.roomId)) {
            grouped.set(item.roomId, []);
        }
        grouped.get(item.roomId).push(item);
    }

    return grouped;
}

const barsByRoom = computed(() => byRoom(layout.value.bars));
const overbookingsByRoom = computed(() => byRoom(layout.value.overbookings));
const overbookingCount = computed(() => layout.value.overbookings.length);
const overbookingCountLabel = computed(() => countLabel(overbookingCount.value, 'Overbooking', 'Overbookings'));

function groupHasOverbooking(group) {
    return group.rooms.some((room) => overbookingsByRoom.value.has(room.id));
}

/** Identifies an Overbooking's highlight in the DOM, for scrolling to it. */
function overbookingKey(overbooking) {
    return `${overbooking.roomId}-${overbooking.start}`;
}

function rowHeight(roomId) {
    const lanes = layout.value.laneCounts.get(roomId) ?? 1;

    return 2 * ROW_PADDING + lanes * BAR_HEIGHT + (lanes - 1) * LANE_GAP;
}

function overbookingStyle(overbooking) {
    return {
        left: `${overbooking.start * DAY_WIDTH}px`,
        width: `${(overbooking.end - overbooking.start) * DAY_WIDTH}px`,
    };
}

const scroller = ref(null);

/** Scrolls the first Overbooking of the window into view, expanding its Property group if needed. */
async function scrollToFirstOverbooking() {
    const first = layout.value.overbookings[0];
    if (!first) {
        return;
    }
    const group = props.groups.find((candidate) => candidate.rooms.some((room) => room.id === first.roomId));
    if (group) {
        collapsed.delete(group.id);
    }
    await nextTick();
    scroller.value
        ?.querySelector(`[data-overbooking="${overbookingKey(first)}"]`)
        ?.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
}

const collapsed = reactive(new Set());

function toggleGroup(groupId) {
    if (collapsed.has(groupId)) {
        collapsed.delete(groupId);
    } else {
        collapsed.add(groupId);
    }
}

function barColor(booking) {
    return booking.color || NEUTRAL_COLOR;
}

/** Picks dark or light text for readable contrast on the bar colour. */
function textColor(background) {
    const match = /^#?([0-9a-f]{3}|[0-9a-f]{6})$/i.exec(background.trim());
    if (!match) {
        return '#ffffff';
    }
    let hex = match[1];
    if (hex.length === 3) {
        hex = hex.split('').map((char) => char + char).join('');
    }
    const [red, green, blue] = [0, 2, 4].map((offset) => parseInt(hex.slice(offset, offset + 2), 16));
    const luminance = (0.299 * red + 0.587 * green + 0.114 * blue) / 255;

    return luminance > 0.6 ? '#111827' : '#ffffff';
}

function barStyle(bar) {
    const background = barColor(bar.booking);
    const left = bar.start * DAY_WIDTH + (bar.clippedStart ? 0 : BAR_INSET);
    const right = bar.end * DAY_WIDTH - (bar.clippedEnd ? 0 : BAR_INSET);

    return {
        top: `${ROW_PADDING + bar.lane * (BAR_HEIGHT + LANE_GAP)}px`,
        height: `${BAR_HEIGHT}px`,
        left: `${left}px`,
        width: `${Math.max(right - left, 4)}px`,
        backgroundColor: background,
        color: textColor(background),
    };
}

function formatDate(date) {
    return longDateFormatter.format(toUtcDate(date));
}

function countLabel(count, singular, plural) {
    return `${count} ${count === 1 ? singular : plural}`;
}

const NO_SOURCE = 'No source';
const NO_STATUS = 'No status';

function barLabel(booking) {
    return [
        booking.guestName,
        countLabel(booking.guests, 'guest', 'guests'),
        `${formatDate(booking.checkIn)} to ${formatDate(booking.checkOut)}`,
        countLabel(booking.nights, 'night', 'nights'),
        costFormatter.format(booking.totalCost),
        `Source: ${booking.sourceName ?? NO_SOURCE}`,
        `Status: ${booking.statusName ?? NO_STATUS}`,
    ].join(', ');
}

const tooltip = ref(null);

function showTooltip(event, booking) {
    if (event.pointerType && event.pointerType !== 'mouse' && event.pointerType !== 'pen') {
        return;
    }
    const rect = event.currentTarget.getBoundingClientRect();
    const tooltipWidth = 256;
    tooltip.value = {
        booking,
        top: rect.bottom + 6,
        left: Math.min(Math.max(8, rect.left), window.innerWidth - tooltipWidth - 8),
    };
}

function hideTooltip() {
    tooltip.value = null;
}
</script>

<template>
    <div class="relative [--room-column:7rem] sm:[--room-column:10rem]">
        <div
            ref="scroller"
            class="max-h-[75vh] overflow-auto overscroll-contain rounded-lg border border-gray-200 bg-white"
            @scroll.passive="hideTooltip"
        >
            <div class="relative" :style="{ width: `calc(var(--room-column) + ${timelineWidth}px)` }">
                <!-- Date header -->
                <div class="sticky top-0 z-30 flex border-b border-gray-200 bg-white">
                    <div class="sticky left-0 z-10 flex w-[var(--room-column)] shrink-0 flex-col items-start justify-between gap-1 border-r border-gray-200 bg-white px-3 py-2">
                        <button
                            v-if="overbookingCount > 0"
                            type="button"
                            class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-red-300 hover:bg-red-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                            :aria-label="`${overbookingCountLabel} in this window. Scroll to the first one.`"
                            :title="`${overbookingCountLabel} in this window`"
                            @click="scrollToFirstOverbooking"
                        >
                            <ExclamationTriangleIcon class="h-3.5 w-3.5" aria-hidden="true" />
                            {{ overbookingCount }}
                            <span class="hidden sm:inline">{{ overbookingCount === 1 ? 'overbooking' : 'overbookings' }}</span>
                        </button>
                        <span v-else class="text-xs text-gray-400">No overbookings</span>
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Rooms</span>
                    </div>
                    <div :style="{ width: `${timelineWidth}px` }">
                        <div class="flex border-b border-gray-100">
                            <div
                                v-for="month in months"
                                :key="month.label"
                                class="truncate border-r border-gray-100 px-2 py-1 text-sm font-semibold text-gray-700"
                                :style="{ width: `${month.days * DAY_WIDTH}px` }"
                            >
                                {{ month.label }}
                            </div>
                        </div>
                        <div class="flex">
                            <div
                                v-for="day in days"
                                :key="day.date"
                                class="flex shrink-0 flex-col items-center border-r border-gray-100 py-1 leading-tight"
                                :class="[
                                    day.isWeekend ? 'bg-gray-100' : '',
                                    showTodayLine && day.index === todayIndex ? 'text-red-600' : 'text-gray-600',
                                ]"
                                :style="{ width: `${DAY_WIDTH}px` }"
                            >
                                <span class="text-[11px]">{{ weekdayFormatter.format(day.utcDate) }}</span>
                                <span class="text-sm font-medium">{{ day.utcDate.getUTCDate() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="groups.length === 0" class="sticky left-0 w-full max-w-md p-6 text-sm text-gray-500">
                    There are no Properties with Rooms to show.
                </div>

                <!-- Property groups -->
                <div v-for="group in groups" :key="group.id">
                    <div class="flex border-b border-gray-200 bg-gray-50">
                        <button
                            type="button"
                            class="sticky left-0 z-20 flex w-[var(--room-column)] shrink-0 items-center gap-1 border-r border-gray-200 bg-gray-50 px-2 py-2 text-left text-sm font-semibold text-gray-800 hover:bg-gray-100"
                            :aria-expanded="!collapsed.has(group.id)"
                            @click="toggleGroup(group.id)"
                        >
                            <svg
                                class="h-4 w-4 shrink-0 transition-transform"
                                :class="collapsed.has(group.id) ? '-rotate-90' : ''"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                            <span class="truncate">{{ group.name }}</span>
                            <ExclamationTriangleIcon
                                v-if="collapsed.has(group.id) && groupHasOverbooking(group)"
                                class="h-4 w-4 shrink-0 text-red-600"
                                role="img"
                                aria-label="Overbooked in this window"
                            />
                        </button>
                        <div class="flex items-center px-2 text-xs text-gray-500" :style="{ width: `${timelineWidth}px` }">
                            <span v-if="collapsed.has(group.id)" class="sticky left-[calc(var(--room-column)+0.5rem)]">
                                {{ countLabel(group.rooms.length, 'room', 'rooms') }} hidden
                            </span>
                        </div>
                    </div>

                    <template v-if="!collapsed.has(group.id)">
                        <div v-if="group.rooms.length === 0" class="flex border-b border-gray-100">
                            <div class="sticky left-0 z-20 w-[var(--room-column)] shrink-0 border-r border-gray-200 bg-white px-3 py-2 text-sm italic text-gray-400">
                                No rooms
                            </div>
                        </div>
                        <div v-for="room in group.rooms" :key="room.id" class="flex border-b border-gray-100">
                            <div
                                class="sticky left-0 z-20 flex w-[var(--room-column)] shrink-0 items-center truncate border-r border-gray-200 bg-white px-3 pl-7 text-sm text-gray-700"
                                :style="{ height: `${rowHeight(room.id)}px` }"
                                :title="room.name"
                            >
                                <span class="truncate">{{ room.name }}</span>
                                <ExclamationTriangleIcon
                                    v-if="overbookingsByRoom.has(room.id)"
                                    class="ml-1 h-4 w-4 shrink-0 text-red-600"
                                    role="img"
                                    aria-label="Overbooked in this window"
                                />
                            </div>
                            <div class="relative" :style="{ width: `${timelineWidth}px`, height: `${rowHeight(room.id)}px` }">
                                <!-- Day cells -->
                                <div class="absolute inset-0 flex" aria-hidden="true">
                                    <div
                                        v-for="day in days"
                                        :key="day.date"
                                        class="h-full shrink-0 border-r border-gray-100"
                                        :class="day.isWeekend ? 'bg-gray-100' : ''"
                                        :style="{ width: `${DAY_WIDTH}px` }"
                                    />
                                </div>

                                <!-- Booking bars -->
                                <Link
                                    v-for="bar in barsByRoom.get(room.id) ?? []"
                                    :key="bar.booking.id"
                                    :href="bar.booking.editUrl"
                                    class="absolute z-10 flex items-center gap-1 overflow-hidden whitespace-nowrap px-2 text-xs font-medium shadow-sm ring-1 ring-black/10 hover:brightness-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                    :class="[
                                        bar.clippedStart ? 'rounded-l-none' : 'rounded-l-md',
                                        bar.clippedEnd ? 'rounded-r-none' : 'rounded-r-md',
                                    ]"
                                    :style="barStyle(bar)"
                                    :aria-label="barLabel(bar.booking)"
                                    @pointerenter="showTooltip($event, bar.booking)"
                                    @pointerleave="hideTooltip"
                                    @focus="showTooltip($event, bar.booking)"
                                    @blur="hideTooltip"
                                >
                                    <span class="truncate">{{ bar.booking.guestName }}</span>
                                    <span class="shrink-0 opacity-80">· {{ bar.booking.guests }}</span>
                                </Link>

                                <!-- Shared Nights of Overbookings -->
                                <div
                                    v-for="overbooking in overbookingsByRoom.get(room.id) ?? []"
                                    :key="overbooking.start"
                                    :data-overbooking="overbookingKey(overbooking)"
                                    class="overbooking-pattern pointer-events-none absolute inset-y-0 z-[12] border-x-2 border-red-600"
                                    :style="overbookingStyle(overbooking)"
                                    aria-hidden="true"
                                />
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Today line -->
                <div
                    v-if="showTodayLine"
                    class="pointer-events-none absolute bottom-0 top-0 z-[15] w-0.5 bg-red-500/70"
                    :style="{ left: `calc(var(--room-column) + ${(todayIndex + 0.5) * DAY_WIDTH - 1}px)` }"
                    aria-hidden="true"
                />
            </div>
        </div>

        <!-- Tooltip -->
        <div
            v-if="tooltip"
            class="pointer-events-none fixed z-50 w-64 rounded-md bg-gray-900 px-3 py-2 text-xs text-white shadow-lg"
            :style="{ top: `${tooltip.top}px`, left: `${tooltip.left}px` }"
            role="tooltip"
        >
            <p class="mb-1 text-sm font-semibold">{{ tooltip.booking.guestName }}</p>
            <dl class="grid grid-cols-[auto,1fr] gap-x-3 gap-y-0.5">
                <dt class="text-gray-400">Check-in</dt>
                <dd>{{ formatDate(tooltip.booking.checkIn) }}</dd>
                <dt class="text-gray-400">Check-out</dt>
                <dd>{{ formatDate(tooltip.booking.checkOut) }}</dd>
                <dt class="text-gray-400">Nights</dt>
                <dd>{{ tooltip.booking.nights }}</dd>
                <dt class="text-gray-400">Guests</dt>
                <dd>{{ tooltip.booking.guests }}</dd>
                <dt class="text-gray-400">Total cost</dt>
                <dd>{{ costFormatter.format(tooltip.booking.totalCost) }}</dd>
                <dt class="text-gray-400">Source</dt>
                <dd>{{ tooltip.booking.sourceName ?? NO_SOURCE }}</dd>
                <dt class="text-gray-400">Status</dt>
                <dd class="flex items-center gap-1">
                    <span class="inline-block h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: barColor(tooltip.booking) }" />
                    {{ tooltip.booking.statusName ?? NO_STATUS }}
                </dd>
            </dl>
        </div>
    </div>
</template>

<style scoped>
.overbooking-pattern {
    background-image: repeating-linear-gradient(
        135deg,
        rgb(220 38 38 / 0.45) 0,
        rgb(220 38 38 / 0.45) 3px,
        transparent 3px,
        transparent 8px
    );
}
</style>
