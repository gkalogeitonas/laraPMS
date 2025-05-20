<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";

// Import the DangerButton component
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import NavLink from "@/Components/NavLink.vue";
import BookingForm from "@/Pages/Bookings/BookingForm.vue";

const props = defineProps([
    "rooms",
    "bookingStatuses",
    "BookingSources",
    "booking" // New prop for existing booking data
]);

let form = useForm({
    name: props.booking.name || "",
    email: props.booking.email || "",
    phone: props.booking.phone || "",
    address: props.booking.address || "",
    customer_id: props.booking.customer_id || "",
    booking_status_id: props.booking.booking_status_id || "",
    booking_source_id: props.booking.booking_source_id || "",
    check_in: props.booking.check_in || "",
    check_out: props.booking.check_out || "",
    total_guests: props.booking.total_guests || "",
    price: props.booking.price || "",
    room_id: props.booking.room_id || ""
});

let submit = () => {
    form.put(route('bookings.update', props.booking.id));
};

const deleteBooking = () => {
    if (confirm('Are you sure you want to delete this booking?')) {
        form.delete(route('bookings.destroy', props.booking.id));
    }
};
</script>

<template>
    <Head title="Edit Booking" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Booking
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <BookingForm :form="form" :rooms="rooms" :bookingStatuses="bookingStatuses" :BookingSources="BookingSources" />
                    <div class="flex items-center justify-between mt-4">
                        <DangerButton
                            type="button"
                            @click="deleteBooking"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            class="hidden md:block"
                        >
                            Delete Booking
                        </DangerButton>
                        <!-- Empty div for small screens to ensure layout consistency -->
                        <div class="md:hidden"></div>

                        <div class="flex items-center">
                            <NavLink :href="route('bookings.index')" class=""
                                >Cancel</NavLink
                            >
                            <PrimaryButton
                                class="ms-4"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Update Booking
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
