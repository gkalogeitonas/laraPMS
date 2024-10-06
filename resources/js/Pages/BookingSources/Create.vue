<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

import { useForm } from "@inertiajs/vue3";

// Import the necessary components
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NavLink from "@/Components/NavLink.vue";
import BookingSourceForm from "@/Pages/BookingSources/BookingSourceForm.vue";

let form = useForm({
    name: "",
});

let submit = () => {
    form.post(route("booking-sources.store"));
};
</script>

<template>
    <Head title="Create Booking Status" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Booking Source
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <BookingSourceForm :form="form" />
                    <div class="flex items-center justify-end mt-4">
                        <NavLink :href="route('booking-sources.index')" class=""
                            >Cancel</NavLink
                        >
                        <PrimaryButton
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Create Booking Source
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
