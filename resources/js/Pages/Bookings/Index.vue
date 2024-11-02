<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch, computed} from 'vue';



// Import the DangerButton component
import AddButton from '@/Components/AddButton.vue';
import BookingList from '@/Components/BookingList.vue';
import BookingFilters from '@/Components/BookingFilters.vue';

import { router } from '@inertiajs/vue3'
import { difference } from 'lodash';




const props = defineProps({
    bookings: Object,
    bookingStatuses: Object,
    BookingSources: Object,
    Rooms: Object,
    filters: Object,
    totals: Object,
});

const filters = ref({
    name: props.filters.name || '',
    booking_status_id: props.filters.booking_status_id || '',
    booking_source_id: props.filters.booking_source_id || '',
    room_id: props.filters.room_id || '',
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
});


const handleDateRangeChange = (dateRange) => {
    filters.value.start_date = dateRange[0];
    filters.value.end_date = dateRange[1];
};

const updateFilters = (newFilters) => {
    filters.value = newFilters;
};

const getNonEmptyFilters = (filters) => {
    return Object.fromEntries(
        Object.entries(filters).filter(([key, value]) => value !== '' && value !== null && value !== undefined)
    );
};

watch(filters, (newFilters) => {
    const nonEmptyFilters = getNonEmptyFilters(newFilters);
    router.get(route('bookings.index'), nonEmptyFilters, { preserveState: true });
}, { deep: true });

</script>

<template>


    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bookings</h2>
            <AddButton :href="route('bookings.create')">Create Booking</AddButton>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                <!-- filter-->
                <BookingFilters
                :filters="filters"
                :bookingStatuses="bookingStatuses"
                :BookingSources="BookingSources"
                :Rooms="Rooms"
                @update:filters="updateFilters"
                />
                <!-- /filter-->
                <!-- Booking List -->
                <BookingList :bookings="bookings" :totals="totals" />
                <!-- /Booking List -->
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>
/* Custom grid template columns */
.grid-cols-11 {
  grid-template-columns: 25px 120px repeat(8, 1fr) 205px;
}
</style>
