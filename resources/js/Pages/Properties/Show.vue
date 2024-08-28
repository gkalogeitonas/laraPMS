<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteButton from '@/Components/DeleteButton.vue';
import NavLink from '@/Components/NavLink.vue';


// Import the DangerButton component

// Assuming rooms are passed as a prop
const props = defineProps({
  property: Array,
  rooms: Array,
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{property.name}}</h2>
        </template>

        <div class="py-12">
            <h1 class="text-2xl font-bold mb-4">{{ property.name }}</h1>
            <p class="mb-2"><strong>Address:</strong> {{ property.address }}</p>
            <p class="mb-2"><strong>Description:</strong> {{ property.description }}</p>
            <p class="mb-2"><strong>Type:</strong> {{ property.type }}</p>
            <div class="mt-4 flex ">
                <NavLink href="/properties" class="mr-4">Back to List</NavLink>
                <NavLink :href="route('properties.edit', property.id)" class="mr-4">Edit</NavLink>
                <DeleteButton :href="route('properties.destroy', property.id)" class="ml-auto">Delete</DeleteButton>
            </div>
            <h2 class="text-xl font-bold mt-8 mb-4">Rooms</h2>
            <ul>
                <li v-for="room in rooms" :key="room.id">
                    <p class="mb-2"><strong>Name:</strong> {{ room.name }}</p>
                    <div class="mt-4 flex ">
                        <NavLink :href="route('rooms.edit', room.id)" class="mr-4">Edit</NavLink>
                        <DeleteButton :href="route('rooms.destroy', room.id)" class="ml-auto">Delete</DeleteButton>
                    </div>
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>
