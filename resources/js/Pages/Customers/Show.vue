<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteButton from '@/Components/DeleteButton.vue';
import NavLink from '@/Components/NavLink.vue';

// Import the DangerButton component

// Assuming customers are passed as a prop
const props = defineProps({
  customer: Array,
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{customer.name}}</h2>
        </template>
        <div>
            <p>Email: {{ customer.email }}</p>
            <p>Telephone: {{ customer.telephone }}</p>
            <p>Address: {{ customer.address }}</p>
        </div>
    </AuthenticatedLayout>
</template>
