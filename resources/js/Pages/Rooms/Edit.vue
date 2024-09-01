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
import RoomForm from '@/Pages/Rooms/RoomForm.vue';



const props = defineProps(['room','types', 'statuses']);

const form = useForm({
    name: props.room.name,
    type: props.room.type,
    status: props.room.status,
    description: props.room.description,
});

let submit = () => {
  form.patch(route('rooms.update',props.room.id));
};



</script>
<template>
<Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Room</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <RoomForm :form="form" :types="types" :statuses="statuses" />

                    <div class="flex items-center justify-end mt-4">
                        <NavLink :href="route('properties.show', props.room.property_id)" class="">Cancel</NavLink>
                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Edit Room
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
