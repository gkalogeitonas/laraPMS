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
import Autocomplete from '@/Components/Autocomplete.vue';


const props = defineProps([
    "rooms",
    "statuses",
]);

let form = useForm({
    name: "",
    email: "",
    phone: "",
    address: "",
    customer_id: "",
    status: "",
    start_date: "",
    end_date: "",
    price: "",
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="customer_id" value="Customer" />
                            <!-- <select v-model="form.customer_id" id="customer_id" name="customer_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select a customer</option>
                                <option v-for="customer in customers" :value="customer.id" :key="customer.id">
                                    {{ customer.name }}
                                </option>
                            </select> -->
                            <Autocomplete apiEndpoint="/customer/search" />
                            <InputError :message="form.errors.customer_id" />
                        </div>
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput v-model="form.name" id="name" type="text" />
                            <InputError :message="form.errors.name" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <InputLabel for="start_date" value="Start Date" />
                            <TextInput v-model="form.start_date" id="start_date" type="date" />
                            <InputError :message="form.errors.start_date" />
                        </div>
                        <div>
                            <InputLabel for="end_date" value="End Date" />
                            <TextInput v-model="form.end_date" id="end_date" type="date" />
                            <InputError :message="form.errors.end_date" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <InputLabel for="price" value="Price" />
                            <TextInput v-model="form.price" id="price" type="number" />
                            <InputError :message="form.errors.price" />
                        </div>
                        <div>
                            <InputLabel for="total_guests" value="Total Guests" />
                            <TextInput v-model="form.total_guests" id="total_guests" type="number" />
                            <InputError :message="form.errors.total_guests" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <InputLabel for="phone" value="Phone" />
                            <TextInput v-model="form.phone" id="phone" type="text" />
                            <InputError :message="form.errors.phone" />
                        </div>
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput v-model="form.email" id="email" type="email" />
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <InputLabel for="status" value="Status" />
                            <select v-model="form.status" id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select a status</option>
                                <option v-for="status in statuses" :value="status" :key="status">
                                    {{ status }}
                                </option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>
                        <div>
                            <InputLabel for="source" value="Source" />
                            <TextInput v-model="form.source" id="source" type="text" />
                            <InputError :message="form.errors.source" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
