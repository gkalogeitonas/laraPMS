<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Import the necessary components
import AddButton from '@/Components/AddButton.vue';
import ActionButtons from '@/Components/ActionButtons.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    bookingStatuses: Object,
});


</script>

<template>
    <Head title="Booking Statuses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Booking Statuses</h2>
            <AddButton :href="route('booking-statuses.create')">Create New Booking Status</AddButton>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Booking Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="status in bookingStatuses" :key="status.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ status.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 text-white bg-gray-200 rounded-full" :style="{ backgroundColor: status.color }">
                                        {{ status.color }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-4 mt-4">
                                        <ActionButtons
                                        :editUrl="route('booking-statuses.edit', status.id)"
                                        :deleteUrl="route('booking-statuses.destroy', status.id)"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Pagination :links="bookingStatuses.links" class="mt-6 flex justify-end mr-5" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
