<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import NavLink from '@/Components/NavLink.vue';

// Define the props
const props = defineProps({
  room: Object,
  currentBooking: Object,
  upcomingBookings: Array,
  recentBookings: Array,
  occupancyRate: Number,
  averageRate: Number,
  totalRevenue: Number,
  currentMonthRevenue: Number,
  bookingTrends: Array,
  isOccupied: Boolean
});

// Method to format currency
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(value);
};

// Method to format dates
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Set up chart (when component is mounted)
onMounted(() => {
  if (props.bookingTrends && props.bookingTrends.length > 0) {
    renderBookingChart();
  }
});

// Render simple booking chart
const renderBookingChart = () => {
  const chartContainer = document.getElementById('bookingChartContainer');
  if (!chartContainer) return;

  chartContainer.innerHTML = '';

  const chartHeader = document.createElement('div');
  chartHeader.className = 'flex justify-between items-center mb-2';
  chartHeader.innerHTML = '<h3 class="text-lg font-semibold">Bookings by Month</h3>';
  chartContainer.appendChild(chartHeader);

  const chartBars = document.createElement('div');
  chartBars.className = 'flex items-end h-40 space-x-2';

  const months = props.bookingTrends.map(item => item.month);
  const counts = props.bookingTrends.map(item => item.count);
  const maxCount = Math.max(...counts, 1); // Avoid division by zero

  counts.forEach((count, index) => {
    const height = count > 0 ? (count / maxCount) * 100 : 5;

    const barContainer = document.createElement('div');
    barContainer.className = 'flex flex-col items-center flex-1';

    const bar = document.createElement('div');
    bar.className = 'bg-blue-500 w-full rounded-t';
    bar.style.height = `${height}%`;
    bar.title = `${count} bookings in ${months[index]}`;

    const label = document.createElement('div');
    label.className = 'text-xs mt-1';
    label.innerText = months[index];

    const value = document.createElement('div');
    value.className = 'text-xs font-semibold';
    value.innerText = count;

    barContainer.appendChild(value);
    barContainer.appendChild(bar);
    barContainer.appendChild(label);

    chartBars.appendChild(barContainer);
  });

  chartContainer.appendChild(chartBars);
};

</script>

<template>
  <Head :title="room.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ room.name }} <span class="text-sm text-gray-500">in {{ room.property.name }}</span>
        </h2>
        <div class="flex space-x-2 ml-auto">
          <Link :href="`/rooms/${room.id}/edit`" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm">
            Edit Room
          </Link>
          <DeleteButton :href="`/rooms/${room.id}`" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md text-sm">
            Delete Room
          </DeleteButton>
        </div>
      </div>
    </template>

    <div class="py-6 space-y-6">
      <!-- Room Status and Key Info -->
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <div class="flex flex-col md:flex-row md:justify-between">
            <!-- Room Details -->
            <div class="md:w-1/2 space-y-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-800">Room Details</h3>
                <p class="text-sm text-gray-600">{{ room.description || 'No description available' }}</p>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-500">Room Type</p>
                  <p class="font-medium">{{ room.type }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Base Rate</p>
                  <p class="font-medium">{{ formatCurrency(room.price || averageRate) }}</p>
                </div>
              </div>

              <div class="pt-2">
                <div class="inline-flex items-center px-3 py-1 rounded-full"
                  :class="isOccupied ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                  <span class="w-2 h-2 mr-2 rounded-full"
                    :class="isOccupied ? 'bg-red-500' : 'bg-green-500'"></span>
                  <span>{{ isOccupied ? 'Occupied' : 'Available' }}</span>
                </div>
              </div>
            </div>

            <!-- Current Booking (if occupied) -->
            <div v-if="isOccupied" class="md:w-1/2 mt-4 md:mt-0 bg-gray-50 p-4 rounded-md">
              <h3 class="text-lg font-semibold text-gray-800">Current Guest</h3>
              <div class="mt-2 space-y-2">
                <p>
                  <span class="font-medium">{{ currentBooking.name }}</span>
                  <span v-if="currentBooking.bookingStatus"
                    class="ml-2 px-2 py-1 text-xs rounded-full"
                    :style="{ backgroundColor: currentBooking.bookingStatus.color + '20',
                            color: currentBooking.bookingStatus.color }">
                    {{ currentBooking.bookingStatus.name }}
                  </span>
                </p>
                <p class="text-sm">
                  Check-in: <span class="font-medium">{{ formatDate(currentBooking.check_in) }}</span>
                </p>
                <p class="text-sm">
                  Check-out: <span class="font-medium">{{ formatDate(currentBooking.check_out) }}</span>
                </p>
                <p v-if="currentBooking.notes" class="text-sm text-gray-600 italic">
                  "{{ currentBooking.notes }}"
                </p>
                <Link :href="`/bookings/${currentBooking.id}`"
                  class="inline-block mt-2 text-sm text-blue-600 hover:underline">
                  View Booking Details →
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Key Stats Cards -->
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Occupancy Rate -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-500">30-Day Occupancy</p>
                  <p class="text-2xl font-bold">{{ occupancyRate }}%</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
              </div>
              <p class="mt-2 text-sm text-gray-500">Last 30 days</p>
            </div>
          </div>

          <!-- Average Rate -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-500">Average Rate</p>
                  <p class="text-2xl font-bold">{{ formatCurrency(averageRate) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
              <p class="mt-2 text-sm text-gray-500">Per booking</p>
            </div>
          </div>

          <!-- Monthly Revenue -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
                  <p class="text-2xl font-bold">{{ formatCurrency(currentMonthRevenue) }}</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
              </div>
              <p class="mt-2 text-sm text-gray-500">Current month</p>
            </div>
          </div>

          <!-- Total Revenue -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                  <p class="text-2xl font-bold">{{ formatCurrency(totalRevenue) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
              </div>
              <p class="mt-2 text-sm text-gray-500">All time</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Booking Trends & Upcoming Bookings -->
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Booking Trends -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg md:col-span-2">
            <div class="p-6">
              <div id="bookingChartContainer" class="h-64">
                <!-- Chart will be rendered here by JS -->
              </div>
            </div>
          </div>

          <!-- Upcoming Bookings -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4">Upcoming Bookings</h3>
              <div class="space-y-3">
                <div v-if="upcomingBookings.length === 0" class="text-gray-500">
                  No upcoming bookings scheduled
                </div>
                <div v-for="booking in upcomingBookings" :key="booking.id" class="border-b border-gray-200 pb-2 last:border-b-0 last:pb-0">
                  <Link :href="`/bookings/${booking.id}`" class="block hover:bg-gray-50 py-2">
                    <div class="flex items-center">
                      <div class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-2">
                        {{ formatDate(booking.check_in) }}
                      </div>
                      <span class="font-medium">{{ booking.name }}</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                      {{ formatDate(booking.check_in) }} - {{ formatDate(booking.check_out) }}
                    </div>
                  </Link>
                </div>
              </div>
              <Link href="/bookings" class="text-blue-600 hover:text-blue-800 text-sm font-medium block text-center mt-4">
                View All Bookings
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Bookings -->
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Booking History</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Guest
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Dates
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Amount
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="recentBookings.length === 0">
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                      No booking history found
                    </td>
                  </tr>
                  <tr v-for="booking in recentBookings" :key="booking.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <Link :href="`/bookings/${booking.id}`" class="text-blue-600 hover:text-blue-900">
                        {{ booking.name }}
                      </Link>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(booking.check_in) }} - {{ formatDate(booking.check_out) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span v-if="booking.bookingStatus" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :style="{ backgroundColor: booking.bookingStatus.color + '20',
                                color: booking.bookingStatus.color }">
                        {{ booking.bookingStatus.name }}
                      </span>
                      <span v-else class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                        Unknown
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                      {{ formatCurrency(booking.price) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <Link href="/bookings" class="text-blue-600 hover:text-blue-800 text-sm font-medium block text-center mt-4">
              View All Bookings
            </Link>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-3">
              <Link :href="`/bookings/create?room_id=${room.id}`" class="inline-block px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                Create New Booking
              </Link>
              <Link :href="`/rooms/${room.id}/edit`" class="inline-block px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                Edit Room Details
              </Link>
              <Link :href="`/properties/${room.property.id}`" class="inline-block px-4 py-2 border border-gray-300 hover:bg-gray-50 rounded-md">
                View Property
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
