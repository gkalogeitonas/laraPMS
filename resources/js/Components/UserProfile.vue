<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
</script>

<template>
    <div class="px-4 py-4">
        <DropdownLink :href="route('profile.edit')" class="block px-4 py-4">
            <div class="font-medium text-base text-gray-100">
                {{ $page.props.auth.user.name }}
            </div>
            <div class="font-medium text-sm text-gray-400">{{ $page.props.auth.user.email }}</div>
        </DropdownLink>
        <div class="mt-auto  py-4">
            <ResponsiveNavLink :href="route('logout')" method="post"  as="button">
                Log Out
            </ResponsiveNavLink>
        </div>
    </div>
</template>
