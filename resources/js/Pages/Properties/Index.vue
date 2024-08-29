<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Import the DangerButton component
import DangerButton from '@/Components/DangerButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import NavLink from '@/Components/NavLink.vue';
import EditButton from '@/Components/EditButton.vue';

// Assuming rooms are passed as a prop
const props = defineProps({
  properties: Array,
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Properties</h2>
            <NavLink :href="route('properties.create')" class="text-green-600 hover:text-green-900">Create New Property</NavLink>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Property Name
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="property in props.properties" :key="property.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <NavLink :href="route('properties.show', property.id)">{{ property.name }}</NavLink>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <EditButton :href="route('properties.edit', property.id)">
                                      Edit
                                    </EditButton>
                                    <DeleteButton :href="route('properties.destroy', property.id)">
                                      Delete
                                    </DeleteButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
