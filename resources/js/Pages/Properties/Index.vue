<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Import the DangerButton component
import DangerButton from '@/Components/DangerButton.vue';
import NavLink from '@/Components/NavLink.vue';
import AddButton from '@/Components/AddButton.vue';
import ActionButtons from '@/Components/ActionButtons.vue';

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
            <AddButton :href="route('properties.create')">Create New Property</AddButton>
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
                                    <div class="flex justify-end space-x-4 mt-4">
                                        <ActionButtons
                                        :editUrl="route('properties.edit', property.id)"
                                        :deleteUrl="route('properties.destroy', property.id)"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
