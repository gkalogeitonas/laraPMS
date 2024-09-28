<script setup>
import { ref } from 'vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NavigationMenu from '@/Components/NavigationMenu.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';


const isSidebarOpen = ref(false);
</script>

<template>
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white flex flex-col">
        <!-- Toggle Button for Small Screens -->
        <button @click="isSidebarOpen = !isSidebarOpen" class="md:hidden p-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
        <div :class="{ 'block': isSidebarOpen, 'hidden': !isSidebarOpen }"
            class="w-64 bg-gray-800 text-white flex flex-col md:block">

            <!-- Logo -->
            <div class="flex  items-center">
                <Link :href="route('dashboard')" class="mx-auto">
                    <ApplicationLogo class="w-20 h-20 fill-current text-gray-500 " />
                </Link>
            </div>


            <!-- Navigation Links -->
            <!-- Navigation Menu Component -->
            <NavigationMenu />
        </div>
    </div>

</template>
