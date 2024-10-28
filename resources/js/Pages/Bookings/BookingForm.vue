<template>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <InputLabel for="customer_id" value="Customer" />
            <Autocomplete apiEndpoint="/customer/search"  paramName="search" @update:customer="updateCustomer" />
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
    <hr class="my-8" />
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
    <div class="grid grid-cols-3 gap-4 mt-4">
        <div>
            <InputLabel for="room_id" value="Room" />
            <select v-model="form.room_id" id="room_id" name="room_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              <option value="">Select a room</option>
              <option v-for="room in rooms" :value="room.id" :key="room.id">
                {{ room.name }}
              </option>
            </select>
            <InputError :message="form.errors.room_id" />
        </div>
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
            <InputLabel for="status" value="Status" />
            <select v-model="form.booking_status_id" id="bookingStatus" name="bookingStatus" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Select a status</option>
                <option v-for="status in bookingStatuses" :value="status.id" :key="status.id">
                    {{ status.name }}
                </option>
            </select>
            <InputError :message="form.errors.booking_status_id" />
        </div>
        <div>
            <InputLabel for="source" value="Source" />
            <select v-model="form.booking_source_id" id="BookingSource" name="BookingSource" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Select a status</option>
                <option v-for="source in BookingSources" :value="source.id" :key="source.id">
                    {{ source.name }}
                </option>
            </select>
            <InputError :message="form.errors.source" />
        </div>
    </div>
</template>


<script setup>
import { defineProps } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Autocomplete from '@/Components/Autocomplete.vue';

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
  rooms: {
    type: Object,
    required: true,
  },
  bookingStatuses: {
    type: Object,
    required: true,
  },
  BookingSources: {
    type: Object,
    required: true,
  },
});

const updateCustomer = (customer) => {
  //console.log("Selected Customer:", customer); // Debugging with console.log
  //alert(`Selected Customer: ${JSON.stringify(customer)}`); // Uncomment this line to use alert instead
  props.form.customer_id = customer.id;
  props.form.name = customer.name;
  props.form.phone = customer.phone;
  props.form.email = customer.email;
};
</script>
