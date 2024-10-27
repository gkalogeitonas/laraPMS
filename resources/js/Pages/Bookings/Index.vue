<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch, computed} from 'vue';



// Import the DangerButton component
import AddButton from '@/Components/AddButton.vue';
import ActionButtons from '@/Components/ActionButtons.vue';
import Pagination from '@/Components/Pagination.vue';
import BookingStatus from '@/Components/BookingStatus.vue';
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
    check_in: props.filters.check_in || '',
    check_out: props.filters.check_out || '',
});


const handleDateRangeChange = (dateRange) => {
    filters.value.check_in = dateRange[0];
    filters.value.check_out = dateRange[1];
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="min-w-full divide-y divide-gray-200">
                        <div class="bg-gray-50 grid grid-cols-11 gap-4">
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </span>
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Room
                            </span>
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Guest Name
                            </span>
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </span>
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Source
                            </span>
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                check In
                            </span>
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                check Out
                            </span>
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nights
                            </span>
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </span>
                            <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </span>
                            <span class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </span>
                        </div>
                        <div v-for="booking in bookings.data" :key="booking.id" class="grid text-center grid-cols-11 gap-4 py-4">
                                <div class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold ">
                                        #{{ booking.id }}
                                    </span>
                                </div>
                                <div class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold ">
                                        {{ booking.room.name }}
                                    </span>
                                </div>
                                <div class="px-6 py-4">
                                    {{ booking.name }}
                                </div>
                                <div class="px-6 py-4">
                                    <BookingStatus
                                    v-if="booking.booking_status"
                                    :booking_status="booking.booking_status"
                                  />
                                </div>
                                <div class="px-6 py-4">
                                    <span v-if="booking.booking_source" class="text-xs inline-flex flex-col leading-5 font-semibold ">
                                        {{ booking.booking_source.name }}
                                    </span>
                                </div>
                                <div class="px-6 py-4">
                                    <span class="text-xs inline-flex flex-col leading-5 font-semibold ">
                                        {{ booking.check_in }}
                                    </span>
                                </div>
                                <div class="px-6 py-4">
                                    <span class="text-xs inline-flex flex-col leading-5 font-semibold ">
                                        {{ booking.check_out }}
                                    </span>
                                </div>
                                <div class="px-6 py-4">
                                    <span class="text-xs inline-flex flex-col leading-5 font-semibold ">
                                        {{ booking.total_days }}
                                    </span>
                                </div>
                                <div class="px-6 py-4">
                                    <span class="px-2 inline-flex flex-col text-xs leading-5 font-semibold ">
                                        {{ booking.price }}
                                    </span>
                                </div>
                                <div class="px-6 py-4">
                                    <span class="px-2 inline-flex flex-col text-xs leading-5 font-semibold ">
                                        {{booking.total_cost}}
                                    </span>
                                </div>
                                <div class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-4 mt-4">
                                        <ActionButtons
                                            :showUrl="route('bookings.show', booking.id)"
                                            :editUrl="route('bookings.edit', booking.id)"
                                            :deleteUrl="route('bookings.destroy', booking.id)" />
                                    </div>
                                </div>
                        </div>
                    </div>
                    <!-- Display the total sum -->
                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-900">Total Amount: {{ totals.total_amount }}</h3>
                        <h3 class="text-lg font-medium text-gray-900">Total Days: {{ totals.total_days }}</h3>
                    </div>
                    <Pagination :links="bookings.links" class="mt-6 flex justify-end mr-5" />
                </div>
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
