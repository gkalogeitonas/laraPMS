<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import TenantSwitcher from '@/Components/TenantSwitcher.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
</script>


<template>
    <div class="flex h-screen">

        <!-- Sidebar -->
        <Sidebar />
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
