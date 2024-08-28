<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteButton from '@/Components/DeleteButton.vue';
import NavLink from '@/Components/NavLink.vue';

// Import the DangerButton component

// Assuming rooms are passed as a prop
const props = defineProps({
  room: Array,
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{room.name}}</h2>
        </template>

        <div class="py-12">
            <div class="room-details p-6 max-w-lg mx-auto bg-white rounded-xl shadow-md space-y-4">
                <h1 class="text-2xl font-bold">{{ room.name }}</h1>
                <p class="text-gray-700">{{ room.description }}</p>
                <p class="text-gray-900"><strong>Status:</strong> {{ room.status }}</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
