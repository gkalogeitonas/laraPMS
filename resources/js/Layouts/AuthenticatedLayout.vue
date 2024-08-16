<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="px-4 py-4">
                <a :href="route('profile.edit')" class="block px-4 py-4">
                    <div class="font-medium text-base text-gray-100">
                        {{ $page.props.auth.user.name }}
                    </div>
                    <div class="font-medium text-sm text-gray-400">{{ $page.props.auth.user.email }}</div>
                </a>
            </div>
            <div class="mt-3 space-y-1">
                <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    Dashboard
                </ResponsiveNavLink>
                <ResponsiveNavLink  :href="route('rooms.index')" :active="route().current('rooms')">
                      My Rooms
                </ResponsiveNavLink>
                <ResponsiveNavLink  :href="route('calendar')" :active="route().current('calendar')">
                     Calendar
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('logout')" method="post"  as="button">
                    Log Out
                </ResponsiveNavLink>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="flex justify-between items-center p-4 bg-gray-100">
                <button @click="showingNavigationDropdown = !showingNavigationDropdown">
                    <svg class="h-6 w-6 text-gray-500" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></svg>
                </button>
            </div>
            <div class="p-4">
                <slot />
            </div>
        </div>
    </div>
</template>
