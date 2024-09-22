<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';



// Import the DangerButton component
import NavLink from '@/Components/NavLink.vue';
import AddButton from '@/Components/AddButton.vue';
import ActionButtons from '@/Components/ActionButtons.vue';
import Pagination from '@/Components/Pagination.vue';
import { router } from '@inertiajs/vue3'
import debounce from "lodash/debounce";
import Show from '../Customers/Show.vue';




const props = defineProps({
    bookings: Object,
    //filters: Object,
});

// let search = ref(props.filters.search);


// watch(search, debounce(function (value) {
//     router.get('/bookings', { search: value }, {
//         preserveState: true,
//         replace: true });
// }, 300));

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bookings</h2>
            <AddButton :href="route('bookings.create')">Create Booking</AddButton>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- <input v-model="search" type="text" placeholder="Search..." class="border px-2 rounded-lg" /> -->
                    <div class="min-w-full divide-y divide-gray-200">
                        <div class="bg-gray-50 grid grid-cols-7 gap-4">
                            <span class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                               ID
                            </span>
                            <span class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                               Room
                            </span>
                            <span class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                               Guest Name
                            </span>
                            <span class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </span>
                            <span class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dates
                            </span>
                            <span class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price (per/night)
                            </span>
                            <span class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </span>
                        </div>
                        <div class="bg-white divide-y divide-gray-200">
                            <div v-for="booking in bookings.data" :key="booking.id" class="grid grid-cols-7 gap-4 py-4">
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
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ booking.status }}
                                    </span>
                                </div>
                                <div class="px-6 py-4">
                                    <span class="text-xs inline-flex flex-col leading-5 font-semibold ">
                                        <div>Day In: {{ booking.check_in }}</div>
                                        <div>Day Out: {{ booking.check_out }}</div>
                                        <div>Total Night: {{ booking.total_days }}</div>
                                    </span>
                                </div>
                                <div class="px-6 py-4">
                                    <span class="px-2 inline-flex flex-col text-xs leading-5 font-semibold ">
                                        <div class="block">{{ booking.price }}</div>
                                        <div class="block">Total: {{booking.total_cost}}</div>
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
                    </div>
                    <Pagination :links="bookings.links" class="mt-6 flex justify-end mr-5" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>
/* Custom grid template columns */
.grid-cols-7 {
  grid-template-columns: 25px repeat(5, 1fr) 250px;
}
</style>
