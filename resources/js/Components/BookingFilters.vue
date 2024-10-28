<template>
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
                    <select v-model="filters.booking_source_id" id="booking_source" name="booking_source" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import RoomSelect from '@/Components/RoomSelect.vue';
import DateRangePicker from '@/Components/DateRangePicker.vue';

const props = defineProps({
    filters: Object,
    bookingStatuses: Array,
    BookingSources: Array,
    Rooms: Array,
});

const emits = defineEmits(['update:filters']);

const handleDateRangeChange = (dateRange) => {
    emits('update:filters', { ...props.filters, start_date: dateRange[0], end_date: dateRange[1] });
};
</script>
