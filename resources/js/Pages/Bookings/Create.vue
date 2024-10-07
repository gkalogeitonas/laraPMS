<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

import { useForm } from "@inertiajs/vue3";

// Import the DangerButton component
import DangerButton from "@/Components/DangerButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NavLink from "@/Components/NavLink.vue";
import BookingForm from "@/Pages/Bookings/BookingForm.vue";


const props = defineProps([
    "rooms",
    "bookingStatuses",
    "BookingSources"
]);

let form = useForm({
    name: "",
    email: "",
    phone: "",
    address: "",
    customer_id: "",
    booking_status_id: "",
    check_in: "",
    check_out: "",
    total_guests: "",
    price: "",
    room_id: ""
});

let submit = () => {
    form.post(route('bookings.store'));
};


</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                New Booking
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <BookingForm :form="form" :rooms="rooms" :bookingStatuses="bookingStatuses" :BookingSources="BookingSources" />
                    <div class="flex items-center justify-end mt-4">
                        <NavLink :href="route('bookings.index')" class=""
                            >Cancel</NavLink
                        >
                        <PrimaryButton
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Create Booking
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
