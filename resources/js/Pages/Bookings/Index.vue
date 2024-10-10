<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';



// Import the DangerButton component
import NavLink from '@/Components/NavLink.vue';
import AddButton from '@/Components/AddButton.vue';
import ActionButtons from '@/Components/ActionButtons.vue';
import Pagination from '@/Components/Pagination.vue';
import BookingStatus from '@/Components/BookingStatus.vue';
import DateRangePicker from '@/Components/DateRangePicker.vue';
import { router } from '@inertiajs/vue3'
import { difference } from 'lodash';




const props = defineProps({
    bookings: Object,
    //filters: Object,
});

// let search = ref(props.filters.search);


const  handleDateRangeChange = (dateRange) => {
    console.log(dateRange);
    router.get('/bookings', { check_in: dateRange[0], check_out: dateRange[1] }, {
        preserveState: true,
        replace: true
    });
}

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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input v-model="name" type="text" placeholder="Name..." class="border px-2 rounded-lg" />
                            </div>
                            <div>
                            </div>
                            <div>
                                <DateRangePicker @update:dateRange="handleDateRangeChange" />
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
