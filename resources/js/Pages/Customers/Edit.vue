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
import CustomerForm from "@/Pages/Customers/CustomerForm.vue";

const props = defineProps(["customer"]);

let form = useForm({
    id: props.customer.id,
    name: props.customer.name,
    email: props.customer.email,
    phone: props.customer.phone,
    address: props.customer.address,
});

let submit = () => {
    form.patch(route('customers.update',props.customer.id));
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit  Customer
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <CustomerForm :form="form" />
                    <div class="flex items-center justify-end mt-4">
                        <div class="flex items-center justify-end mt-4">
                            <NavLink :href="route('customers.show', customer.id)" class="">Cancel</NavLink>
                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Edit customer
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
