<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Link } from '@inertiajs/vue3';
import Chart from 'chart.js/auto';

const props = defineProps({
    recentBookings: Array,
    propertyStatistics: Array,
    upcomingCheckouts: Array,
    upcomingCheckins: Array,
    occupancyRate: Number,
    totalRevenue: Number,
    totalProperties: Number,
    totalRooms: Number,
    totalCustomers: Number,
    bookingsChart: Array,
    roomStatistics: Array,
    hasActiveTenant: Boolean
});

// Method to format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};

// Method to format dates
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Set up chart (when component is mounted)
let chartInstance = null;

onMounted(() => {
    if (props.hasActiveTenant && props.bookingsChart.length > 0) {
        renderChart();
    }
});

onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});

const renderChart = () => {
    const ctx = document.getElementById('bookingsChart');
    if (!ctx) return;

    const months = props.bookingsChart.map(item => item.month);
    const counts = props.bookingsChart.map(item => item.count);

    // Destroy any existing chart
    if (chartInstance) {
        chartInstance.destroy();
    }

    // Create new chart
    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Number of Bookings',
                data: counts,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Monthly Bookings'
                },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItem) {
                            return tooltipItem[0].label;
                        },
                        label: function(context) {
                            return `${context.parsed.y} bookings`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div v-if="!hasActiveTenant" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="mb-4">No active tenant selected. Please select or create a tenant to view dashboard information.</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="space-y-6">
            <!-- Key Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Occupancy Rate -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Occupancy Rate</p>
                                <p class="text-2xl font-bold">{{ occupancyRate }}%</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Current room occupancy</p>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
                                <p class="text-2xl font-bold">{{ formatCurrency(totalRevenue) }}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Current month</p>
                    </div>
                </div>

                <!-- Total Properties -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Properties</p>
                                <p class="text-2xl font-bold">{{ totalProperties }}</p>
                            </div>
                            <div class="bg-indigo-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Total properties managed</p>
                    </div>
                </div>

                <!-- Total Rooms -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Rooms</p>
                                <p class="text-2xl font-bold">{{ totalRooms }}</p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Total rooms available</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Checkouts & Check-ins -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Upcoming Check-ins -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Upcoming Check-ins</h3>
                        <div class="space-y-3">
                            <div v-if="upcomingCheckins.length === 0" class="text-gray-500">
                                No upcoming check-ins in the next 7 days
                            </div>
                            <div v-for="checkin in upcomingCheckins" :key="checkin.id" class="border-b border-gray-200 pb-2 last:border-b-0 last:pb-0">
                                <Link :href="`/bookings/${checkin.id}`" class="block hover:bg-gray-50 py-2">
                                    <div class="flex items-center">
                                        <div class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-2">
                                            {{ formatDate(checkin.check_in) }}
                                        </div>
                                        <span class="font-medium">{{ checkin.name }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        {{ checkin.room?.name }} in {{ checkin.room?.property?.name }}
                                    </div>
                                </Link>
                            </div>
                        </div>
                        <Link href="/bookings" class="text-blue-600 hover:text-blue-800 text-sm font-medium block text-center mt-4">
                            View All Bookings
                        </Link>
                    </div>
                </div>

                <!-- Upcoming Checkouts -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Upcoming Checkouts</h3>
                        <div class="space-y-3">
                            <div v-if="upcomingCheckouts.length === 0" class="text-gray-500">
                                No upcoming checkouts in the next 7 days
                            </div>
                            <div v-for="checkout in upcomingCheckouts" :key="checkout.id" class="border-b border-gray-200 pb-2 last:border-b-0 last:pb-0">
                                <Link :href="`/bookings/${checkout.id}`" class="block hover:bg-gray-50 py-2">
                                    <div class="flex items-center">
                                        <div class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium mr-2">
                                            {{ formatDate(checkout.check_out) }}
                                        </div>
                                        <span class="font-medium">{{ checkout.name }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        {{ checkout.room?.name }} in {{ checkout.room?.property?.name }}
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

            <!-- Graph & Recent Bookings -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">
                <!-- Bookings Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Monthly Bookings</h3>
                        <div class="h-64">
                            <canvas id="bookingsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Recent Bookings</h3>
                        <div class="space-y-3">
                            <div v-if="recentBookings.length === 0" class="text-gray-500">
                                No recent bookings found
                            </div>
                            <div v-for="booking in recentBookings" :key="booking.id" class="border-b border-gray-200 pb-2 last:border-b-0 last:pb-0">
                                <Link :href="`/bookings/${booking.id}`" class="block hover:bg-gray-50 py-2">
                                    <div class="flex justify-between">
                                        <span class="font-medium">{{ booking.name }}</span>
                                        <span :class="`px-2 py-1 rounded-full text-xs ${booking.booking_status?.color ? 'bg-[' + booking.booking_status.color + '10]' : 'bg-gray-100'}`">
                                            {{ booking.booking_status?.name || 'Unknown' }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ booking.room?.name }} in {{ booking.room?.property?.name }}
                                    </div>
                                    <div class="text-sm text-gray-500 flex justify-between mt-1">
                                        <span>{{ formatDate(booking.check_in) }} - {{ formatDate(booking.check_out) }}</span>
                                        <span class="font-medium">{{ formatCurrency(booking.price) }}</span>
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

            <!-- Property Statistics -->
            <div class="grid grid-cols-1 gap-4 mt-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Property Statistics</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Property
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total Rooms
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Occupied
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Available
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Occupancy
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="propertyStatistics.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No properties found
                                        </td>
                                    </tr>
                                    <tr v-for="property in propertyStatistics" :key="property.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="`/properties/${property.id}`" class="text-blue-600 hover:text-blue-900">
                                                {{ property.name }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{ property.totalRooms }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{ property.occupiedRooms }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{ property.availableRooms }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${property.occupancyRate}%`"></div>
                                                </div>
                                                <span>{{ property.occupancyRate }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Statistics -->
            <div class="grid grid-cols-1 gap-4 mt-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Room Statistics</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Room
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Property
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rate
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Avg. Rate
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            30d Occupancy
                                        </th>
                                        <th class="px-4 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Next Booking
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="roomStatistics.length === 0">
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No rooms found
                                        </td>
                                    </tr>
                                    <tr v-for="room in roomStatistics" :key="room.id">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <Link :href="`/rooms/${room.id}`" class="text-blue-600 hover:text-blue-900">
                                                {{ room.name }}
                                            </Link>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            {{ room.propertyName }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            {{ room.type }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                            {{ formatCurrency(room.price) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                            {{ formatCurrency(room.averageRate) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span :class="room.currentlyOccupied ?
                                                'bg-red-100 text-red-800' :
                                                'bg-green-100 text-green-800'"
                                                class="px-2 py-1 rounded-full text-xs">
                                                {{ room.currentlyOccupied ? 'Occupied' : 'Available' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${room.occupancyRate}%`"></div>
                                                </div>
                                                <span>{{ room.occupancyRate }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                            <span v-if="room.nextBookingDate">
                                                {{ formatDate(room.nextBookingDate) }}
                                            </span>
                                            <span v-else class="text-gray-500">-</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
