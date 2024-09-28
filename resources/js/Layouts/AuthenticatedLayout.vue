<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import TenantSwitcher from '@/Components/TenantSwitcher.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import UserProfile from '@/Components/UserProfile.vue';
</script>


<template>
    <div class="flex h-full">

        <!-- Sidebar -->
        <Sidebar id="sidebar" />
        <!-- Page Heading-->


        <!-- Main Content -->
        <div class="flex-1">

            <header class="bg-white shadow flex justify-end pr-4">
                <div class=" py-2 px-4 sm:px-6 lg:px-8 text-gray-900">
                    <TenantSwitcher
                    :tenants="$page.props.auth.tenants"
                    :activeTenant="$page.props.auth.activeTenant"
                    />
                </div>
                <UserProfile class=" py-2 px-4 sm:px-6 lg:px-8"  />
            </header>



            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between">
                <slot name="header" />
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

<style>
#sidebar {
    min-height: 100vh;
}
</style>
