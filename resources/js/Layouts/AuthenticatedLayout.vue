<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import { Link } from '@inertiajs/vue3';
import TenantSwitcher from '@/Components/TenantSwitcher.vue';

const showingNavigationDropdown = ref(false);
const isSidebarOpen = ref(false);
</script>

<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white flex flex-col">
            <!-- Toggle Button for Small Screens -->
            <button @click="isSidebarOpen = !isSidebarOpen" class="md:hidden p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
            <div :class="{'block': isSidebarOpen, 'hidden': !isSidebarOpen}" class="w-64 bg-gray-800 text-white flex flex-col md:block">
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
                    <ResponsiveNavLink  :href="route('properties.index')" :active="route().current('properties')">
                            Properties
                    </ResponsiveNavLink>
                    <ResponsiveNavLink  :href="route('rooms.index')" :active="route().current('rooms')">
                            Rooms
                    </ResponsiveNavLink>
                    <ResponsiveNavLink  :href="route('customers.index')" :active="route().current('rooms')">
                            Customers
                    </ResponsiveNavLink>
                    <ResponsiveNavLink  :href="route('calendar')" :active="route().current('calendar')">
                        Calendar
                    </ResponsiveNavLink>
                </div>
                <div class="mt-auto  py-4">
                    <ResponsiveNavLink :href="route('logout')" method="post"  as="button">
                        Log Out
                    </ResponsiveNavLink>
                </div>
            </div>
        </div>

                <!-- Page Heading-->


        <!-- Main Content -->
        <div class="flex-1">
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between">
                    <slot name="header" />
                </div>
            </header>
            <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8   flex justify-end">
                <TenantSwitcher
                :tenants="$page.props.auth.tenants"
                :activeTenant="$page.props.auth.activeTenant"
                />
            </div>

            <!-- Flash Messages Component -->
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <FlashMessages />
            </div>

            <div class="p-4">
                <slot />
            </div>
        </div>
    </div>
</template>
