<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import OccupancyCalendar from '@/Components/OccupancyCalendar/OccupancyCalendar.vue';
import { addDays, toDayNumber, toUtcDate } from '@/Components/OccupancyCalendar/layout.js';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    groups: { type: Array, required: true },
    bookings: { type: Array, required: true },
    window: { type: Object, required: true },
});

const WINDOW_SIZES = [
    { days: 14, label: '2 weeks' },
    { days: 30, label: '1 month' },
    { days: 90, label: '3 months' },
];

const rangeFormatter = new Intl.DateTimeFormat('en', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC' });

/** Calendar URL for a window; without a start the server picks the default start. */
function windowUrl(start, days) {
    return route('calendar', start ? { start, days } : { days });
}

const previousUrl = computed(() => windowUrl(addDays(props.window.start, -props.window.days), props.window.days));
const nextUrl = computed(() => windowUrl(addDays(props.window.start, props.window.days), props.window.days));
const todayUrl = computed(() => windowUrl(null, props.window.days));

const rangeLabel = computed(() => {
    const first = rangeFormatter.format(toUtcDate(props.window.start));
    const last = rangeFormatter.format(toUtcDate(addDays(props.window.start, props.window.days - 1)));

    return `${first} – ${last}`;
});

/** Why the last Reschedule was refused, shown until the next one or until dismissed. */
const rescheduleError = ref(null);

/**
 * Saves a Reschedule. The bar moves at once (optimistic update) and Inertia moves it back
 * if the server refuses the change; the server's message is then shown.
 */
function reschedule({ bookingId, roomId, checkIn, checkOut }) {
    rescheduleError.value = null;

    router.patch(
        route('bookings.reschedule', bookingId),
        { room_id: roomId, check_in: checkIn, check_out: checkOut },
        {
            preserveScroll: true,
            preserveState: true,
            optimistic: (pageProps) => ({
                bookings: pageProps.bookings.map((booking) =>
                    booking.id === bookingId
                        ? { ...booking, roomId, checkIn, checkOut, nights: toDayNumber(checkOut) - toDayNumber(checkIn) }
                        : booking,
                ),
            }),
            onError: (errors) => {
                rescheduleError.value = Object.values(errors)[0] ?? 'The Booking could not be rescheduled.';
            },
        },
    );
}

const navButton = 'inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:z-10';
</script>

<template>
    <Head title="Calendar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Calendar</h2>
        </template>

        <div class="mx-auto max-w-full">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-3">
                    <nav class="inline-flex rounded-md shadow-sm" aria-label="Move the calendar window">
                        <Link :href="previousUrl" preserve-scroll :class="[navButton, 'rounded-l-md']" aria-label="Previous window">
                            &lsaquo; Previous
                        </Link>
                        <Link :href="todayUrl" preserve-scroll :class="[navButton, '-ml-px']">Today</Link>
                        <Link :href="nextUrl" preserve-scroll :class="[navButton, '-ml-px rounded-r-md']" aria-label="Next window">
                            Next &rsaquo;
                        </Link>
                    </nav>
                    <span class="text-sm font-medium text-gray-700">{{ rangeLabel }}</span>
                </div>

                <nav class="inline-flex rounded-md shadow-sm" aria-label="Window size">
                    <Link
                        v-for="(size, index) in WINDOW_SIZES"
                        :key="size.days"
                        :href="windowUrl(window.start, size.days)"
                        preserve-scroll
                        :aria-current="size.days === window.days ? 'true' : undefined"
                        :class="[
                            navButton,
                            index === 0 ? 'rounded-l-md' : '-ml-px',
                            index === WINDOW_SIZES.length - 1 ? 'rounded-r-md' : '',
                            size.days === window.days ? '!bg-indigo-600 !text-white !border-indigo-600' : '',
                        ]"
                    >
                        {{ size.label }}
                    </Link>
                </nav>
            </div>

            <div
                v-if="rescheduleError"
                class="mb-3 flex items-start justify-between gap-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                role="alert"
            >
                <p><span class="font-semibold">The Booking was not rescheduled.</span> {{ rescheduleError }}</p>
                <button
                    type="button"
                    class="shrink-0 rounded font-medium text-red-700 hover:text-red-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                    @click="rescheduleError = null"
                >
                    Dismiss
                </button>
            </div>

            <OccupancyCalendar :groups="groups" :bookings="bookings" :window="window" @reschedule="reschedule" />
        </div>
    </AuthenticatedLayout>
</template>
