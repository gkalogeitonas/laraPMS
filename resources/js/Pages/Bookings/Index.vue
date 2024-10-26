<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch, computed} from 'vue';



// Import the DangerButton component
import NavLink from '@/Components/NavLink.vue';
import AddButton from '@/Components/AddButton.vue';
import ActionButtons from '@/Components/ActionButtons.vue';
import Pagination from '@/Components/Pagination.vue';
import BookingStatus from '@/Components/BookingStatus.vue';
import DateRangePicker from '@/Components/DateRangePicker.vue';
import RoomSelect from '@/Components/RoomSelect.vue';
import { router } from '@inertiajs/vue3'
import { difference } from 'lodash';




const props = defineProps({
    bookings: Object,
    bookingStatuses: Object,
    BookingSources: Object,
    Rooms: Object,
    //filters: Object,
});

const filters = ref({
    name: '',
    booking_status_id: '',
    booking_source_id: '',
    room_id: '',
    check_in: '',
    check_out: '',
});


const handleDateRangeChange = (dateRange) => {
    filters.value.check_in = dateRange[0];
    filters.value.check_out = dateRange[1];
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div id="booking_name_filter">
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input v-model="filters.name" type="text" placeholder="Name..." class="border px-2 rounded-lg" />
                            </div>
                            <div id="booking_status_filter">
                                <label for="booking_status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select v-model="filters.booking_status_id" id="booking_status" name="booking_status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a status</option>
                                    <option v-for="status in bookingStatuses" :value="status.id" :key="status.id">
                                        {{ status.name }}
                                    </option>
                                </select>
                            </div>
                            <div id="booking_source_filter">
                                <label for="booking_source" class="block text-sm font-medium text-gray-700">Source</label>
                                <select v-model="filters.booking_source_id"  id="booking_source" name="booking_source" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a source</option>
                                    <option v-for="source in BookingSources" :value="source.id" :key="source.id">
                                        {{ source.name }}
                                    </option>
                                </select>
                            </div>
                            <div id="booking_room_filter">
                                <label for="room" class="block text-sm font-medium text-gray-700">Room</label>
                                <RoomSelect :rooms="Rooms" v-model="filters.room_id" />
                            </div>
                        </div>

                        <div class="flex justify-center mt-5">
                            <div id="booking_date_filter">
                                <label for="date" class="block text-sm font-medium text-gray-700">Date Range</label>
                                <DateRangePicker @change="handleDateRangeChange" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /filter-->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- <input v-model="search" type="text" placeholder="Search..." class="border px-2 rounded-lg" /> -->
                    <div class="min-w-full divide-y divide-gray-200">
                        <div class="bg-gray-50 grid grid-cols-10 gap-4">
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
                        <div v-for="booking in bookings.data" :key="booking.id" class="grid text-center grid-cols-10 gap-4 py-4">
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
                    <Pagination :links="bookings.links" class="mt-6 flex justify-end mr-5" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>
/* Custom grid template columns */
.grid-cols-10 {
  grid-template-columns: 25px 120px repeat(7, 1fr) 205px;
}
</style>
