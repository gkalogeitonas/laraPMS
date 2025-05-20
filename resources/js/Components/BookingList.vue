<!-- BookingList.vue -->
<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="min-w-full divide-y divide-gray-200">
      <div class="bg-gray-50 grid grid-template-columns gap-4">
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider column-id">ID</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Room</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Guest Name</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider column-status">Status</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider column-source">Source</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider column-check-in">Check In</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider column-check-out">Check Out</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider column-dates">Dates</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider column-nights">Nights</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider column-price">Price</span>
        <span class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider column-total">Total</span>
        <span class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</span>
      </div>
      <div v-for="booking in bookings.data" :key="booking.id" class="grid grid-template-columns gap-4 py-4 text-center">
        <div class="px-6 py-4 column-id">
          <span class="px-2 inline-flex text-xs leading-5 font-semibold">#{{ booking.id }}</span>
        </div>
        <div class="px-6 py-4">
          <span class="px-2 inline-flex text-xs leading-5 font-semibold">{{ booking.room.name }}</span>
        </div>
        <div class="px-6 py-4">{{ booking.name }}</div>
        <div class="px-6 py-4 column-status">
          <BookingStatus v-if="booking.booking_status" :booking_status="booking.booking_status" />
        </div>
        <div class="px-6 py-4 column-source">
          <span v-if="booking.booking_source" class="text-xs inline-flex flex-col leading-5 font-semibold">{{ booking.booking_source.name }}</span>
        </div>
        <!-- Check-in date (hidden on mobile) -->
        <div class="px-6 py-4 column-check-in">
          <span class="text-xs inline-flex flex-col leading-5 font-semibold">{{ booking.check_in }}</span>
        </div>
        <!-- Check-out date (hidden on mobile) -->
        <div class="px-6 py-4 column-check-out">
          <span class="text-xs inline-flex flex-col leading-5 font-semibold">{{ booking.check_out }}</span>
        </div>
        <!-- Combined dates (visible only on mobile) -->
        <div class="px-6 py-4 column-dates">
          <div class="flex flex-col">
            <span class="text-xs font-semibold mb-1">In: {{ booking.check_in }}</span>
            <span class="text-xs font-semibold">Out: {{ booking.check_out }}</span>
          </div>
        </div>
        <div class="px-6 py-4 column-nights">
          <span class="text-xs inline-flex flex-col leading-5 font-semibold">{{ booking.total_days }}</span>
        </div>
        <div class="px-6 py-4 column-price">
          <span class="px-2 inline-flex flex-col text-xs leading-5 font-semibold">{{ booking.price }}</span>
        </div>
        <div class="px-6 py-4 column-total">
          <span class="px-2 inline-flex flex-col text-xs leading-5 font-semibold">{{ booking.total_cost }}</span>
        </div>
        <div class="px-6 py-4 text-right text-sm font-medium">
          <div class="flex justify-end space-x-4 mt-4">
            <ActionButtons
              :editUrl="route('bookings.edit', booking.id)"
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
/* Grid layout for different screen sizes */
.grid-template-columns {
  grid-template-columns: repeat(12, 1fr);
}

/* By default, hide the combined dates column */
.column-dates {
  display: none;
}

/* Hide specific columns on small screens and adjust grid */
@media (max-width: 740px) {
  .column-id,
  .column-status,
  .column-source,
  .column-nights,
  .column-price,
  .column-total,
  .column-check-in,
  .column-check-out {
    display: none;
  }

  /* Show the combined dates column on mobile */
  .column-dates {
    display: block;
  }

  .grid-template-columns {
    grid-template-columns:
      1.5fr  /* Room - more space */
      2fr    /* Guest Name - more space */
      2fr    /* Combined Dates - more space */
      1fr;   /* Actions - same space */
  }
}
</style>
