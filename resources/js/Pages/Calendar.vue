<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import OccupancyCalendar from '@/Components/OccupancyCalendar/OccupancyCalendar.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    groups: { type: Array, required: true },
    bookings: { type: Array, required: true },
    window: { type: Object, required: true },
});
</script>

<template>
    <Head title="Calendar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Calendar</h2>
        </template>

        <div class="mx-auto max-w-full">
            <OccupancyCalendar :groups="groups" :bookings="bookings" :window="window" />
        </div>
    </AuthenticatedLayout>
</template>
