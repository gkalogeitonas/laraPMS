<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Import the DangerButton component

// Assuming rooms are passed as a prop
const props = defineProps({
  property: Array,
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{property.name}}</h2>
        </template>

        <div class="py-12">
        </div>
    </AuthenticatedLayout>
</template>
