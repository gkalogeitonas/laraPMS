<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import { useForm } from "@inertiajs/vue3";

// Import the DangerButton component
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import NavLink from '@/Components/NavLink.vue';

const form = useForm({
  name: '',
  type: '',
  status: 'available',
  description: ''
});

const props = defineProps(['property','types', 'statuses']);

let submit = () => {
  form.post(route('properties.rooms.store',props.property.id));
};



</script>
<template>
<Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Room</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">

                    <div class="mb-4">
                        <InputLabel for="name" value="Room Name" />
                        <input type="text" id="name" v-model="form.name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="type" value="Room Type" />
                        <select id="type" v-model="form.type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option v-for="type in types" :key="type" :value="type">{{ type }}</option>
                        </select>
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="status" value="Room Status" />
                        <select id="status" v-model="form.status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                        </select>
                        <InputError :message="form.errors.status" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="description" value="Description" />
                        <textarea id="description" v-model="form.description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="3"></textarea>
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <NavLink :href="route('properties.show', props.property.id)" class="">Cancel</NavLink>
                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Create Room
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
<style scoped>
/* Add any additional styles if needed */
</style>
