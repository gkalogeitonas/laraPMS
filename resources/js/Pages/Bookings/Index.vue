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
            <AddButton :href="route('bookings.create')">Create  Booking</AddButton>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- <input v-model="search" type="text" placeholder="Search..." class="border px-2 rounded-lg" /> -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bookings
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="booking in bookings.data" :key="booking.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <NavLink :href="route('bookings.show', booking.id)">{{ booking.name }}</NavLink>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-4 mt-4">
                                        <ActionButtons
                                        :editUrl="route('bookings.edit', booking.id)"
                                        :deleteUrl="route('bookings.destroy', booking.id)"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Pagination :links="bookings.links" class="mt-6 flex justify-end mr-5" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
