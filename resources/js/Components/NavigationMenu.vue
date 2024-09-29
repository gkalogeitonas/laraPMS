<script setup>
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { ref } from 'vue';


const isSettingsMenuOpen = ref(false);

function toggleSettingsMenu() {
  isSettingsMenuOpen.value = !isSettingsMenuOpen.value;
}
</script>

<template>
    <div class="mt-3 space-y-1">
        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Dashboard
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('properties.index')" :active="route().current('properties')">
            Properties
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('rooms.index')" :active="route().current('rooms')">
            Rooms
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('customers.index')" :active="route().current('rooms')">
            Customers
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('calendar')" :active="route().current('calendar')">
            Calendar
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('bookings.index')" :active="route().current('bookings')">
            Bookings
        </ResponsiveNavLink>
        <div @click="toggleSettingsMenu" class="cursor-pointer">
            <span :class="{
                ' block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 text-start text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out': isSettingsMenuOpen,
                ' text-gay-800 block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out': !isSettingsMenuOpen
                }">
              Settings
            </span>
        </div>
          <div v-if="isSettingsMenuOpen" class="ml-6">
            <ResponsiveNavLink href="#" :active="route().current('settings.profile')">
              Profile
            </ResponsiveNavLink>
            <ResponsiveNavLink href="#" :active="route().current('settings.account')">
              Account
            </ResponsiveNavLink>
        </div>
    </div>
</template>
