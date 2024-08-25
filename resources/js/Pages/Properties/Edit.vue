<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import { useForm } from "@inertiajs/vue3";

// Import the DangerButton component
import DangerButton from '@/Components/DangerButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import NavLink from '@/Components/NavLink.vue';
import PropertyForm from '@/Pages/Properties/PropertyForm.vue';

const props = defineProps(['property']);

let form = useForm({
  name: props.property.name,
  address: props.property.address,
  description: props.property.description,
  type: props.property.type,
});



let submit = () => {
  form.patch(route('properties.update', props.property.id));
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Property</h2>
        </template>

         <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <PropertyForm :form="form" />
                    <div class="flex items-center justify-between -mb-8 -mr-6 p-8">
                        <NavLink href="/properties" class="">Cancel</NavLink>
                        <div class="flex items-center">
                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Update Property
                            </PrimaryButton>
                            <DeleteButton class="ml-3" :href="route('properties.destroy', property.id)">
                                Delete
                            </DeleteButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
