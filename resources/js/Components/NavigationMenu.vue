<script setup>
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
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
    </div>
</template>
