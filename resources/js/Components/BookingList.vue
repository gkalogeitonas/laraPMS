<!-- BookingList.vue -->
<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="min-w-full divide-y divide-gray-200">
      <div class="bg-gray-50 grid grid-cols-11 gap-4">
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ID</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Room</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Guest Name</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Source</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nights</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Price</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</span>
        <span class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</span>
      </div>
      <div v-for="booking in bookings.data" :key="booking.id" class="grid text-center grid-cols-11 gap-4 py-4">
        <div class="px-6 py-4">
          <span class="px-2 inline-flex text-xs leading-5 font-semibold">#{{ booking.id }}</span>
        </div>
        <div class="px-6 py-4">
          <span class="px-2 inline-flex text-xs leading-5 font-semibold">{{ booking.room.name }}</span>
        </div>
        <div class="px-6 py-4">{{ booking.name }}</div>
        <div class="px-6 py-4">
          <BookingStatus v-if="booking.booking_status" :booking_status="booking.booking_status" />
        </div>
        <div class="px-6 py-4">
          <span v-if="booking.booking_source" class="text-xs inline-flex flex-col leading-5 font-semibold">{{ booking.booking_source.name }}</span>
        </div>
        <div class="px-6 py-4">
          <span class="text-xs inline-flex flex-col leading-5 font-semibold">{{ booking.check_in }}</span>
        </div>
        <div class="px-6 py-4">
          <span class="text-xs inline-flex flex-col leading-5 font-semibold">{{ booking.check_out }}</span>
        </div>
        <div class="px-6 py-4">
          <span class="text-xs inline-flex flex-col leading-5 font-semibold">{{ booking.total_days }}</span>
        </div>
        <div class="px-6 py-4">
          <span class="px-2 inline-flex flex-col text-xs leading-5 font-semibold">{{ booking.price }}</span>
        </div>
        <div class="px-6 py-4">
          <span class="px-2 inline-flex flex-col text-xs leading-5 font-semibold">{{ booking.total_cost }}</span>
        </div>
        <div class="px-6 py-4 text-right text-sm font-medium">
          <div class="flex justify-end space-x-4 mt-4">
            <ActionButtons
              :showUrl="route('bookings.show', booking.id)"
              :editUrl="route('bookings.edit', booking.id)"
              :deleteUrl="route('bookings.destroy', booking.id)"
            />
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
</template>

<script setup>
import { defineProps } from 'vue';
import ActionButtons from '@/Components/ActionButtons.vue';
import Pagination from '@/Components/Pagination.vue';
import BookingStatus from '@/Components/BookingStatus.vue';

const props = defineProps({
  bookings: {
    type: Object,
    required: true
  },
  totals: {
    type: Object,
    required: true
  }
});
</script>

<style scoped>
/* Add component-specific styles here */
</style>
