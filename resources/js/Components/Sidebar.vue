<script setup>
import { ref } from 'vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NavigationMenu from '@/Components/NavigationMenu.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const isSidebarOpen = ref(true);
</script>

<template>
    <!-- Sidebar Container - Always present but width changes -->
    <div class="bg-gray-800 text-white flex flex-col transition-all duration-300"
         :class="[isSidebarOpen ? 'w-64' : 'w-16']">

        <!-- Toggle Button -->
        <button @click="isSidebarOpen = !isSidebarOpen" class="p-4 flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      :d="isSidebarOpen
                          ? 'M5 6h14M9 12h10M13 18h6M5  12l3 '
                          : 'M4 18H10 M4 12L16 12 M4 6L20 6'" />
            </svg>
        </button>

        <!-- Sidebar Content -->
        <div :class="{ 'block': isSidebarOpen, 'hidden': !isSidebarOpen }">
            <!-- Logo -->
            <div class="flex items-center">
                <Link :href="route('dashboard')" class="mx-auto">
                    <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
                </Link>
            </div>

            <!-- Navigation Links -->
            <NavigationMenu />
        </div>
    </div>
</template>
